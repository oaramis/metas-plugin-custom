<?php 
	/**
	* Create metaboxes in admin
	*/
	class MetasAdmin
	{
		/**
		 * [__construct Initialize options]
		 */
		function __construct()
		{
			add_action( 'admin_enqueue_scripts',  array( $this, 'addScriptsAdmin' ) );
			add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );

			add_action( 'wp_ajax_ajCall',  array( $this, 'ajaxCall') );
			add_action( 'wp_ajax_nopriv_ajCall', array( $this, 'ajaxCall') );
			
			add_action( 'add_meta_boxes', array( $this, 'registerMetaBoxes' ) );
			add_action( 'save_post', array( $this, 'savePostMeta' ) );

		}

		/**
		 * [addScriptsAdmin Add scripts and css in the admin]
		 * @param [type] $hook [description]
		 */
		public function addScriptsAdmin($hook) 
		{
			wp_enqueue_style( 'ccs-multiple-select',  plugin_dir_url( __FILE__ ) . 'inc/multiple-select/css/multi-select.css');
			wp_enqueue_style( 'css-admin',  plugin_dir_url( __FILE__ ) . 'css/metas-admin.css');
			wp_enqueue_script( 'js-multiple-select', plugin_dir_url( __FILE__ ) . 'inc/multiple-select/js/jquery.multi-select.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script( 'add_scripts_admin', plugin_dir_url( __FILE__ ) . 'js/metas-admin.js', array('jquery','jquery-ui-sortable'), '1.0.0', true );

			$data['ajax_url'] = admin_url('admin-ajax.php' );
			wp_localize_script( 'add_scripts_admin', 'variables', $data );
		}
		

		/**
		 * [addAdminMenu Add tab in menu]
		 */
		public function addAdminMenu()
	    {
	        add_menu_page('Create Metas', 'Create Metas', 'manage_options', 'create-metas', array( $this, 'optionsPage' ), 'dashicons-admin-tools', 66 );
	    }

	    /**
	     * [optionsPage description]
	     * @return [type] [description]
	     */
	    public function optionsPage()
	    {
	    	// update_option('metas-custom', '');
	    	// echo "<pre>";
	    	// print_r(self::getDataMetas());
	    	// echo "</pre>";
	    	// exit();
	        if( !empty($_REQUEST['page']) && $_REQUEST['page'] == "create-metas" ) {
	            if (isset($_POST['metas-custom']) && $_POST['metas-custom']) {
	            	self::saveData($_POST['metas-custom']);
	            }
	        }

			$pages = self::getPages();
			$post_types = self::getPostsTypes();

			$allMetas = self::getDataMetas();

	        ?>
	        <div class="wrap container-metas">
	            <h2><?php echo esc_html('Create Metas'); ?></h2>
	            <div id="content-metas">
		             <form action="<?php echo esc_url( admin_url( 'admin.php?page=create-metas' ) ); ?>" method="POST">
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row">Metas created</th>
									<td>
										<?php 
											if ( !empty($allMetas) ) {
										?>
												<select id="allMetas">
													<option value="clear">Metas Creadas</option>
										<?php
													foreach ($allMetas as $key => $value) {
										?>
														<option value="<?php echo $value['_name_meta_box']; ?>"><?php echo $value['_title_meta_box']; ?></option>
										<?php	
													}
										?>
												</select>
										<?php
											}
										?>
									</td>
								</tr>
								<tr>
									<th scope="row">Title Meta Box</th>
									<td>
										<input type="text" name="metas-custom[_title_meta_box]" class="title_meta_box" />
									</td>
								</tr>
								<tr>
									<th scope="row">No. Inputs</th>
									<td>
										<input type="text" name="metas-custom[_no_inputs]" class="no_inputs" />
									</td>
								</tr>
								<tr>
									<th scope="row">No. Editors</th>
									<td>
										<input type="text" name="metas-custom[_no_editors]" class="no_editors" />
									</td>
								</tr>
								<tr>
									<th scope="row">Image Featured</th>
									<td>
										<input type="checkbox" name="metas-custom[_img_featured]"  class="img_featured" value="yes"> Yes
									</td>
								</tr>
								<tr>
									<th scope="row">Gallery</th>
									<td>
										<input type="checkbox" name="metas-custom[_gallery]" value="yes"> Yes
									</td>
								</tr>
								<tr>
									<th scope="row">Video Featured</th>
									<td>
										<input type="checkbox" name="metas-custom[_video_featured]"  class="video_featured" value="yes"> Yes
									</td>
								</tr>
								<tr>
									<th scope="row">Document Featured</th>
									<td>
										<input type="checkbox" name="metas-custom[_document_featured]"  class="document_featured" value="yes"> Yes
									</td>
								</tr>
								<tr>
									<th><h2 class="title">Position of Meta</h2></th>
								</tr>
								<tr>
									<th scope="row">Posts</th>
									<td>
										<input type="checkbox" name="metas-custom[_all_posts]"  class="all_posts_meta" value="yes"> Yes
									</td>
								</tr>
								<tr>
									<th scope="row">Specific Page</th>
									<td>
										<select id="pages" name="metas-custom[_pages][]" class="pages_meta" multiple="multiple">
											<option value="all">All pages</option>
											<?php 
												if (!empty($pages)) {
													foreach ($pages as $key => $page) {
														echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
													}
												}
											?>
										</select>
									</td>
								</tr>
								<?php if (!empty($post_types)) { ?>
									<tr>
										<th scope="row">Specific Post Type</th>
										<td>
											<select id="posts_types" name="metas-custom[_posts_types][]" class="posts_types_meta"  multiple="multiple">
												<option value="all">All post types</option>
												<?php 
													foreach ($post_types as $postType) {
														echo '<option value="'.$postType.'">'.$postType.'</option>';
													}
												?>
											</select>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php submit_button(); ?>
					</form>
				</div>
	        </div>
	        <?php
	    }

	    /**
	     * [getPages Get all pages]
	     * @return [type] [description]
	     */
	    public function getPages()
	    {
	    	$args = array(
				'sort_order' => 'asc',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'child_of' => 0,
				'parent' => -1,
				'post_type' => 'page',
				'post_status' => 'publish'
			); 

			return get_pages($args);
	    }

	    /**
	     * [getPostsTypes Get all posts types]
	     * @return [type] [description]
	     */
	    public function getPostsTypes()
	    {
	    	$args = array(
			   'public'   => true,
			   '_builtin' => false
			);

			$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'
			$post_types = get_post_types( $args, $output, $operator ); 

			return $post_types;
	    }

	    /**
	     * [saveData Save info about metas]
	     * @param  [type] $data [description]
	     * @return [type]       [description]
	     */
	    public function saveData($data)
	    {
	    	$name = self::getNameMetaBox($data['_title_meta_box']);

	    	$content = self::getDataMetas();
	    	$data['_name_meta_box'] = $name;
	    	$content[$name] = $data;
	    	update_option('metas-custom', $content);
	    }

	    /**
	     * [getNameMetaBox Convert the title of meta to name of meta box]
	     * @param  [type] $data [description]
	     * @return [type]       [description]
	     */
	    public function getNameMetaBox($data)
	    {
	    	$name = strtolower($data);
	    	$name = self::removeAccents($name);
	    	$name = str_replace(" ", "_" , $name);
	    	return '_'.$name;
	    }

	    public function removeAccents($name)
	    {
	    	$unwanted_array = array( 'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
			return strtr( $name, $unwanted_array );
	    }

	    /**
	     * [registerMetaBoxes Method for register all metas boxes]
	     * @return [type] [description]
	     */
	    public function registerMetaBoxes()
	    {
	    	$dataMetas = self::getDataMetas();

	    	if ( !empty($dataMetas) ) {
	    		foreach ($dataMetas as $key => $meta) {
	    			/* CHECK IF HAS PAGES */
	    			if ( isset($meta["_pages"]) && !empty($meta["_pages"][0]) ) {
	    				self::createMetasPages($meta);
	    			}

	    			/* POSTS TYPES REGISTER METAS */
	    			if ( isset($meta["_posts_types"]) && !empty($meta["_posts_types"][0]) ) {
	    				self::createMetasPostTypes($meta);
	    			}

	    			/* CHECK IF ALL POSTS IS true */
	    			if ( isset($meta["_all_posts"]) && $meta['_all_posts'] == 'yes'  ) {
	    				self::createMetasPosts($meta);
	    			}
	    		}
	    	}

	    }

	    public function createMetasPosts($meta)
	    {
	    	$nameMetaBox = '';
		    $titleMetaBox = '';
		    $noInputs = '';
		    $noEditors = '';
		    $imgFeatured = '';
		    $videoFeatured = '';
		    $documentFeatured = '';
		    $gallery = '';

		    if (isset($meta['_title_meta_box'])) {
		    	$titleMetaBox = $meta['_title_meta_box'];
		    }

		    if (isset($meta['_no_inputs'])) {
		    	$noInputs = $meta['_no_inputs'];
		    }
		    	
		    if (isset($meta['_no_editors'])) {
		    	$noEditors = $meta['_no_editors'];
		    }

		    if (isset($meta['_name_meta_box'])) {
		    	$nameMetaBox = $meta['_name_meta_box'];    	
		    }

		    if ( isset($meta['_img_featured']) && $meta['_img_featured'] == 'yes' ) {
		    	$imgFeatured = $meta['_img_featured'];    	
		    }

		    if ( isset($meta['_video_featured']) && $meta['_video_featured'] == 'yes' ) {
		    	$videoFeatured = $meta['_video_featured'];    	
		    }

		    if ( isset($meta['_document_featured']) && $meta['_document_featured'] == 'yes' ) {
		    	$documentFeatured = $meta['_document_featured'];    	
		    }

		    if ( isset($meta['_gallery']) && $meta['_gallery'] == 'yes' ) {
		    	$gallery = $meta['_gallery'];    	
		    }

	    	self::addMetaBox($nameMetaBox, $titleMetaBox, 'post' , $noInputs, $noEditors, $imgFeatured, $videoFeatured, $documentFeatured, $gallery);
	    }

	    /**
	     * [createMetasPages Method for register all metas in pages]
	     * @param  [type] $meta [description]
	     * @return [type]       [description]
	     */
	    public function createMetasPages($meta)
	    {
	    	if ( isset($_GET['post']) || isset($_POST['post_ID']) )
		    	$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
		    else
		    	$post_id = null;

		    $nameMetaBox = '';
		    $titleMetaBox = '';
		    $noInputs = '';
		    $noEditors = '';
		    $imgFeatured = '';
		    $videoFeatured = '';
		    $documentFeatured = '';
		    $gallery = '';

		    if (isset($meta['_title_meta_box'])) {
		    	$titleMetaBox = $meta['_title_meta_box'];
		    }

		    if (isset($meta['_no_inputs'])) {
		    	$noInputs = $meta['_no_inputs'];
		    }
		    	
		    if (isset($meta['_no_editors'])) {
		    	$noEditors = $meta['_no_editors'];
		    }

		    if (isset($meta['_name_meta_box'])) {
		    	$nameMetaBox = $meta['_name_meta_box'];    	
		    }

		    if ( isset($meta['_img_featured']) && $meta['_img_featured'] == 'yes' ) {
		    	$imgFeatured = $meta['_img_featured'];    	
		    }

		    if ( isset($meta['_video_featured']) && $meta['_video_featured'] == 'yes' ) {
		    	$videoFeatured = $meta['_video_featured'];    	
		    }

		    if ( isset($meta['_document_featured']) && $meta['_document_featured'] == 'yes' ) {
		    	$documentFeatured = $meta['_document_featured'];    	
		    }

		    if ( isset($meta['_gallery']) && $meta['_gallery'] == 'yes' ) {
		    	$gallery = $meta['_gallery'];    	
		    }

		    foreach ($meta['_pages'] as $key => $value) {
		    	$createMeta = false;
		    	if ($post_id == $value) {
		    		$createMeta = true;
		    	} else if( $value == 'all' ) {
		    		$createMeta = true;
		    	}

		    	if ($createMeta) {
		    		self::addMetaBox($nameMetaBox, $titleMetaBox, 'page' , $noInputs, $noEditors, $imgFeatured, $videoFeatured, $documentFeatured, $gallery);
		    	}
		    }
	    }

	    public function createMetasPostTypes($meta)
	    {
	    	// echo "<pre>";
	    	// print_r($meta);
	    	// echo "</pre>";
	    	// exit();
	    	$nameMetaBox = '';
		    $titleMetaBox = '';
		    $noInputs = '';
		    $noEditors = '';
		    $imgFeatured = '';
		    $videoFeatured = '';
		    $documentFeatured = '';
		    $gallery = '';

		    if (isset($meta['_title_meta_box'])) {
		    	$titleMetaBox = $meta['_title_meta_box'];
		    }

		    if (isset($meta['_no_inputs'])) {
		    	$noInputs = $meta['_no_inputs'];
		    }
		    	
		    if (isset($meta['_no_editors'])) {
		    	$noEditors = $meta['_no_editors'];
		    }

		    if (isset($meta['_name_meta_box'])) {
		    	$nameMetaBox = $meta['_name_meta_box'];    	
		    }

		    if ( isset($meta['_img_featured']) && $meta['_img_featured'] == 'yes' ) {
		    	$imgFeatured = $meta['_img_featured'];    	
		    }

		    if ( isset($meta['_video_featured']) && $meta['_video_featured'] == 'yes' ) {
		    	$videoFeatured = $meta['_video_featured'];    	
		    }

		    if ( isset($meta['_document_featured']) && $meta['_document_featured'] == 'yes' ) {
		    	$documentFeatured = $meta['_document_featured'];    	
		    }

		    if ( isset($meta['_gallery']) && $meta['_gallery'] == 'yes' ) {
		    	$gallery = $meta['_gallery'];    	
		    }
		    foreach ($meta['_posts_types'] as $key => $postType) {
	    		self::addMetaBox($nameMetaBox, $titleMetaBox, $postType , $noInputs, $noEditors, $imgFeatured, $videoFeatured, $documentFeatured, $gallery);
		    }
	    }

	    public function addMetaBox($nameMetaBox, $titleMetaBox, $screen ,$noInputs = '', $noEditors = '', $imgFeatured = '', $videoFeatured = '', $documentFeatured = '', $gallery = '') 
	    {
	    	add_meta_box( $nameMetaBox, $titleMetaBox, array($this, 'metaBoxDefault'), $screen, 'normal', 'default', array( 'name' => $nameMetaBox, 'noInputs' => $noInputs, 'noEditors' => $noEditors, 'imgFeatured' => $imgFeatured, 'videoFeatured' => $videoFeatured, 'documentFeatured' => $documentFeatured, 'gallery' => $gallery ) );
	    }

	    /**
	     * [metaBoxDefault MetaBox with all fields]
	     * @param  [type] $post    [description]
	     * @param  [type] $metabox [description]
	     * @return [type]          [description]
	     */
	    public function metaBoxDefault($post, $metabox)
	    {
	    	$nameMetaBox = $metabox['args']['name'];
	    	$data =  get_post_meta( $post->ID , $nameMetaBox , true );
	    ?>

	    	<div class="container form-wrap">

			
			<!-- SECTION CALL FUNCTION -->
				<div class="call_values">
					<div class="form-field">
						<label>Call function</label>
						<span>get_post_meta( $post->ID , <?php echo $nameMetaBox; ?> , true )</span>
					</div>
				</div>
			<!-- END SECTION CALL FUNCTION -->

			<!-- SECTION IMAGE FEATURED -->
			
			<?php  if ( isset($metabox['args']['imgFeatured']) && $metabox['args']['imgFeatured'] ) {
					$idImage = '';
					$custom_class = $nameMetaBox.'_image_featured';
			?>
					<div class="form-field">
					 	<?php 
			                if( isset($data['id_image_featured']) && $data['id_image_featured'] ) {
			                	$idImage = $data['id_image_featured'];
			            		echo wp_get_attachment_image( $idImage, 'thumbnail', true, array('class' => 'image-attachment') );
			                } 
			            ?>
						<div class="button-primary add_item width_add_image" data-id="<?php echo $custom_class; ?>" data-type="foto" >Agregar Imagen</div>
						<input type="hidden" name="<?php echo $nameMetaBox; ?>[id_image_featured]" class="<?php echo $custom_class; ?>" value="<?php echo $idImage; ?>" />
					</div>
		<?php } ?>

			<!-- END SECTION IMAGE FEATURED -->

			
			<!-- SECTION VIDEO FEATURED -->
			
			<?php  if ( isset($metabox['args']['videoFeatured']) && $metabox['args']['videoFeatured'] ) {
					$idVideo = '';
					$custom_class = $nameMetaBox.'_video_featured';
					
			?>
					<div class="form-field">
					 	<?php 
			                if( isset($data['id_video_featured']) && $data['id_video_featured'] ) {
			                	$idVideo = $data['id_video_featured'];
			                	$attachment_title = get_the_title($idVideo);
			            		echo wp_get_attachment_image( $idVideo, 'thumbnail', true, array('class' => 'image-attachment') );
			            		echo "<label>".$attachment_title."</label>";
			                } 
			            ?>
						<div class="button-primary add_item width_add_image" data-id="<?php echo $custom_class; ?>" data-type="video" >Agregar Video</div>
						<input type="hidden" name="<?php echo $nameMetaBox; ?>[id_video_featured]" class="<?php echo $custom_class; ?>" value="<?php echo $idVideo; ?>" />
					</div>
		<?php } ?>

			<!-- END SECTION VIDEO FEATURED -->

			<!-- SECTION DOCUMENT FEATURED -->
			
			<?php  if ( isset($metabox['args']['documentFeatured']) && $metabox['args']['documentFeatured'] ) {
					$idDocument = '';
					$custom_class = $nameMetaBox.'_document_featured';
			?>
					<div class="form-field">
					 	<?php 
			                if( isset($data['id_document_featured']) && $data['id_document_featured'] ) {
			                	$idDocument = $data['id_document_featured'];
			                	$attachment_title = get_the_title($idDocument);
			            		echo wp_get_attachment_image( $idDocument, 'thumbnail', true, array('class' => 'image-attachment') );
			            		echo "<label>".$attachment_title."</label>";
			                } 
			            ?>
						<div class="button-primary add_item width_add_image" data-id="<?php echo $custom_class; ?>" data-type="pdf" >Agregar Documento</div>
						<input type="hidden" name="<?php echo $nameMetaBox; ?>[id_document_featured]" class="<?php echo $custom_class; ?>" value="<?php echo $idDocument; ?>" />
					</div>
		<?php } ?>

			<!-- END SECTION DOCUMENT FEATURED -->
			

			<!-- SECTION INPUTS -->
			<?php  
				if ( isset($metabox['args']['noInputs']) && !empty($metabox['args']['noInputs']) ) { 
					$c = 0;
					$val = '';
					while ( $c < $metabox['args']['noInputs'] ) {
						if(isset($data['text'][$c]) && !empty($data['text'][$c])) {
							$val = $data['text'][$c];
						} else {
							$val = '';
						}
			?>
						<div class="form-field">
							<label>Titulo</label>
							<input name="<?php echo $nameMetaBox; ?>[text][<?php echo $c; ?>]"  size="40" type="text" value="<?php echo $val; ?>" />
						</div>
			<?php			
						$c++;
					}
			
			 } ?>
			<!-- END SECTION INPUTS -->


			<!-- SECTION EDITORS -->
			<?php  
				if ( isset($metabox['args']['noEditors']) && !empty($metabox['args']['noEditors']) ) {
					$c = 0;
					$val = '';
					$description = '';
					while ( $c < $metabox['args']['noEditors'] ) {
						$description_name = $nameMetaBox."[textarea][$c]";
						$editor_id = 'editor'.$nameMetaBox.'_'.$c;
						if(isset($data) && !empty($data)) {
							$description = $data['textarea'][$c];
						}
			?>
						<div class="form-field">
							<label>Descripción</label>
							<?php wp_editor( htmlspecialchars_decode($description), $editor_id, $settings = array( 'textarea_name'=> $description_name, 'editor_height' => 180 ) ); ?>
						</div>
			<?php 
						$c++;
					} 
				}
			?>
			<!-- END SECTION EDITORS -->

			<!-- SECTION GALLERY -->
			<?php 
				if ( isset($metabox['args']['gallery']) && !empty($metabox['args']['gallery']) ) {
					$idImage = '';
					$custom_class = $nameMetaBox.'_gallery';

					
			?>
					<div class="form-field">
						<div class="button-primary gallery_add width_add_image" data-for="<?php echo $nameMetaBox; ?>" data-id="<?php echo $custom_class; ?>" >Agregar Gallery</div>

						<ul id="gallery<?php echo $nameMetaBox; ?>" class="gallery-wrapper-sortable">
							<?php 
								if(isset($data['gallery_media']) && !empty($data['gallery_media'])) {
									foreach ($data['gallery_media'] as $key => $idImage) {
										$img = wp_get_attachment_image_src( $idImage, 'thumbnail');
							?>	
										<li class="gallery_thumnails">
											<div>
												<span class="gallery-movable"></span>
												<a href="#" class="gallery_remove_item">
													<span>delete</span>
												</a>
												<img src="<?php echo $img[0]; ?>">
												<input type="hidden" name="<?php echo $nameMetaBox; ?>[gallery_media][]" value="<?php echo $idImage ?>" />
											</div>
										</li>
							<?php
									}
								}
							?>
							<!-- <li class="gallery_thumnails">
								<div>
									<span class="gallery-movable"></span>
									<a href="#" class="gallery_remove_item">
										<span>delete</span>
									</a>
									<img src=" ">
									<input type="hidden" name="<?php //echo $nameMetaBox; ?>[gallery_media][]" value="" />
								</div>
							</li> -->
						</ul>
					</div>
			<?php 
				}
			?>
			<!-- END SECTION GALLERY -->

			</div>

		<?php
	    }

	    /**
	     * [savePostMeta Save the metas in each post]
	     * @param  [type] $post_id [description]
	     * @return [type]          [description]
	     */
	    function savePostMeta($post_id)
		{
			$dataMetas = self::getDataMetas();

			foreach ($dataMetas as $key => $meta) {
				$nameMeta = $meta['_name_meta_box'];
				if ( isset($_POST[$nameMeta]) ) { 
					update_post_meta( $post_id, $nameMeta, $_POST[$nameMeta] );
				}
			}
			
		}

		 /**
		 * [ajaxCall Called ajax]
		 * @return [type] [description]
		 */
		public function ajaxCall()
		{
		    switch ($_POST['method']) {
		        case 'getMetaData':
		           	$dataMetas = self::getDataMetas();
		           	$search = $_POST['search'];
		           	foreach ($dataMetas as $key => $v) {
		           		if($v["_name_meta_box"] == $search) {
		           			print_r(json_encode($v));
		           			break;
		           		};
		           		// if ($value['_name_meta_box'] == $search) {
		           		// 
		           		// }
		           		// break;
		           	}
		            die();
		        break;
		    }
		}

		/**
		 * [getDataMetas Return value of Metas saved in the option]
		 * @return [type] [description]
		 */
	    public function getDataMetas()
	    {
	    	return get_option('metas-custom');
	    }

	}
?>