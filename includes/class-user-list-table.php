<?php
if ( ! class_exists ( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Sellers_List extends WP_List_Table {

	/**
	 * ***********************************************************************
	 * Normally we would be querying data from a database and manipulating that
	 * for use in your list table. For this example, we're going to simplify it
	 * slightly and create a pre-built array. Think of this as the data that might
	 * be returned by $wpdb->query()
	 *
	 * In a real-world scenario, you would run your own custom query inside
	 * the prepare_items() method in this class.
	 *
	 * @var array
	 * ************************************************************************
	 */
	

	/**
	 * Sellers_List constructor.
	 *
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'vendedor',     // Singular name of the listed records.
			'plural'   => 'vendedores',    // Plural name of the listed records.
			'ajax'     => false,       // Does this table support ajax?
		) );
	}


    public function SellerFields(){
        $users = get_users( array( 'role__in' => array( 'seller' )));
        $field = array();
        foreach($users as $user) {
            $field[] = array(
                'ID' => $user->ID,
                'vendedor' => $user->display_name,
                'url' => get_site_url() . '/?vendedor=' . $user->ID,
                'whatsapp' => get_user_meta($user->ID, 'whatsapp', true),
                'email' => $user->user_email,
            );
        }
        return $field;
    }

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a `column_cb()` method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', // Render a checkbox instead of text.
			'vendedor'    => _x( 'Vendedor', 'Column label', 'whats-order' ),
			'url'   => _x( 'URL', 'Column label', 'whats-order' ),
			'whatsapp' => _x( 'WhatsApp', 'Column label', 'whats-order' ),
            'email' => _x( 'E-mail', 'Column label', 'whats-order' ),
		);

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within `prepare_items()` and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'vendedor'    => array( 'vendedor', true ),
			'url'   => array( 'url', true ),
			'whatsapp' => array( 'whatsapp', true ),
            'email' => array( 'email', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Get default column value.
	 *
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
            case 'vendedor':
			case 'url':
			case 'whatsapp':
            case 'email':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['ID']                // The value of the checkbox should be the record's ID.
		);
	}

	/**
	 * Get title column value.
	 *
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links are
	 * secured with wp_nonce_url(), as an expected security measure.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_vendedor( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// Build edit row action.
		$edit_query_args = array(
			'page'   => $page,
			'action' => 'edit',
			'vendedor'  => $item['ID'],
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editvendedor_' . $item['ID'] ) ),
			_x( 'Edit', 'List table row action', 'whats-order' )
		);

		// Build delete row action.
		$delete_query_args = array(
			'page'   => $page,
			'action' => 'delete',
			'vendedor'  => $item['ID'],
		);

		$actions['delete'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletevendedor_' . $item['ID'] ) ),
			_x( 'Delete', 'List table row action', 'whats-order' )
		);

		// Return the title contents.
        return sprintf( '<strong><a href="%s">%s</a></strong>%s',
            esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editvendedor_' . $item['ID'] ) ),
			$item['vendedor'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Excluir', 'List table bulk action', 'whats-order' ),
		);

		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 */
    protected function seller_fields() {
        $array = array(
            array(
                'label' => __('Nome'),
                'name'  => 'fname',
                'field' => 'first_name'
            ),
            array(
                'label' => __('Sobrenome'),
                'name'  => 'lname',
                'field' => 'last_name'
            ),
            array(
                'label' => __('Whatsapp'),
                'name'  => 'whats',
                'field' => 'whatsapp'
            ),
            array(
                'label' => __('E-mail'),
                'name'  => 'email',
                'type'  => 'email',
                'field' => 'user_email'
            ),
        );

        return $array;
    }

    protected function url() {
        $admin = get_admin_url();
        $query = $admin. '/admin.php?page=whats-order';

        return $query;
    }


	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {

            if($_POST['step']) {
                foreach($_POST['delete'] as $remove){
                    wp_delete_user( $remove );
                    unset($_POST);
                    header("Location: " . $this->url()); 
                }
                exit;
            }

            $is_bulk = ($_POST['action'] == 'delete') ? true : false;
            $confirm = $is_bulk ? __('Você está prestes a executar uma exclusão em massa dos seguintes vendedores:', 'whats-order') 
            : __('Você está prestes a excluir um vendedor do sistema:', 'whats-order');

            $title = $is_bulk ? __('Exclusão em massa de vendedores', 'whats-order') 
            : __('Excluir vendedor', 'whats-order');

            $delete =  $is_bulk ? $_POST['vendedor'] : $_GET['vendedor'];

            ?>

            <form method="post" action="">

			    <h1 class="wp-heading-inline"><?= $title ?></h1>
                <p><?= $confirm ?></p>
                
                <?php
                if($is_bulk) {
                    foreach($delete as $del) {
                        $user = get_userdata($del);
                        echo '<p>'.$user->display_name.'</p>';
                        echo '<input type="hidden" name="delete[]" value="'.$user->ID.'">';
                    }
                } else {
                    $user = get_userdata($delete);
                    echo '<p>'.$user->display_name.'</p>';
                    echo '<input type="hidden" name="delete[]" value="'.$user->ID.'">';
                }
                ?>
            
                <p class="submit">
                    <input type="hidden" name="action" id="action" value="delete">
                    <button type="submit" name="step" id="step" class="button button-primary" value="confirm">
                        Confirmar exclusão                    
                    </button>
                </p>
            </form>

			<?php wp_die();
		}
	}


    protected function process_edit_action() {
		// Detect when a bulk action is being triggered.
		if ( 'edit' === $this->current_action() ) { 

            if(filter_var($_POST['alteruser'], FILTER_SANITIZE_STRING)) :

                $fields = array(
                    'id'    => filter_var($_GET['vendedor'], FILTER_VALIDATE_INT),
                    'fname' => filter_var($_POST['fname'], FILTER_SANITIZE_STRING),
                    'lname' => filter_var($_POST['lname'], FILTER_SANITIZE_STRING),
                    'whats' => filter_var($_POST['whats'], FILTER_VALIDATE_INT),
                    'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)
                );

                $required = array(
                    'fname',
                    'lname',
                    'whats',
                    'email'
                );

                $message = array();

                foreach($fields as $key => $field) {     
                    if(in_array($key, $required) and empty($field)) {
                        foreach($this->seller_fields() as $input) {
                            if($input['name'] == $key) {
                                $message[] = array(
                                    'text'  => sprintf(__('O campo %s é obrigatório e não pode estar vazio', 'whats-order'), strtolower($input['label'])),
                                    'class' => 'error'
                                );
                            }
                        }  
                    }
                }


                if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL) and !empty($fields['email'])) {
                    $message[] = array(
                        'text'=> __('O e-mail informado é inválido', 'whats-order'),
                        'class' => 'error'
                    );
                }

                
                foreach(get_users( array( 'search' => $fields['id'] ) ) as $user) {
                    $current = array(
                        'whatsapp'  => get_user_meta( $user->ID, 'whatsapp', true),
                        'user_email'=> $user->user_email
                    );

                    foreach(get_users( array( 'meta_value' => $fields['whats'] ) ) as $check) {
                        if($check->ID != $fields['id']){
                            $message[] = array(
                                'text'=> __('O número de WhatsApp informado já pertence a outro vendedor ou usuário do sistema', 'whats-order'),
                                'class' => 'error'
                            );
                        }
                    }

                    foreach(get_users( array( 'search' => $fields['email'] ) ) as $check) {
                        if($check->ID != $fields['id']){
                            $message[] = array(
                                'text'=> __('O e-mail informado já pertence a outro vendedor ou usuário do sistema', 'whats-order'),
                                'class' => 'error'
                            );
                        }
                    }

                    if($current['whatsapp'] != $fields['whats']) {
                        $current['whatsapp'] = $fields['whats'];
                    }

                    if($current['user_email'] != $fields['email']) {
                        $current['user_email'] = $fields['email'];
                    }

                }
                


                if(empty($message)) {
                    $userdata = array(
                        'ID'            => $fields['id'],
                        'user_email'    => $current['user_email']
                    );
                     
                    $user_id = wp_update_user( $userdata );
                    update_user_meta($fields['id'], 'whatsapp', $current['whatsapp']);
                     
                    if (is_wp_error( $user_id ) ) {
                        wp_die(__('Falha ao atualizar vendedor, o erro ocorreu com a função "wp_insert_user", entre em contato com o desenvolvedor para obter suporte', 'whats-order'));
                    }

                    unset($_POST);
                    header("Location: " . $this->url());
                    exit;
                }
               
            endif;


            ?>

            <?php if(!empty($message)) {
                foreach($message as $msg) { ?>
                    <div class="mt-1 custom-notice notice-<?= $msg['class'] ?>">
                        <p class="m-0"><?= $msg['text'] ?></p>
                    </div>
                <?php }
            }?>

            <h1 class="wp-heading-inline"><?php _e('Editar vendedor', 'whats-order'); ?></h1>
            <form action="" method="POST">
                <table class="form-table form-sellers">
                    <tbody>
                        <?php foreach($this->seller_fields() as $fields) : 
                            $user = (array) get_user_by('ID', $_GET['vendedor']);
                            $user = (array) $user['data']; 
                            $user = array_merge($user, get_user_meta($_GET['vendedor']));
                            ?>
                            <tr class="form-field">
                                <th>
                                    <label for="<?= $fields['name'] ?>"><?= $fields['label'] ?></label>
                                </th>
                                <td>
                                    <input  
                                    name="<?= $fields['name'] ?>" 
                                    type="<?= $fields['type'] ? $fields['type'] : 'text' ?>" 
                                    id="<?= $fields['name'] ?>"
                                    <?php 
                                    if($_POST[$fields['name']]) {
                                        echo 'value="'. $_POST[$fields['name']] .'"';
                                    } else {
                                        foreach($user as $key=>$val) {
                                            if($key == $fields['field']) {
                                                if($key == 'user_email') {
                                                    echo 'value="'.$val.'"';
                                                } else {
                                                    echo 'value="'.$val[0].'"';
                                                }   
                                            } 
                                        }
                                    }?> 
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" name="alteruser" id="alteruser" class="button button-primary" value="alteruser">
                            <?php _e('Alterar vendedor'); ?>
                    </button>
                </p>
            </form>
            
			<?php wp_die();
		}
	}



    protected function process_new_action() {

		if ( 'new' === $this->current_action() ) { 
            
            if(filter_var($_POST['newuser'], FILTER_SANITIZE_STRING)) :

                $fields = array(
                    'fname' => filter_var($_POST['fname'], FILTER_SANITIZE_STRING),
                    'lname' => filter_var($_POST['lname'], FILTER_SANITIZE_STRING),
                    'whats' => filter_var($_POST['whats'], FILTER_VALIDATE_INT),
                    'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)
                );

                $required = array(
                    'fname',
                    'lname',
                    'whats',
                    'email'
                );

                $message = array();

                foreach($fields as $key => $field) {     
                    if(in_array($key, $required) and empty($field)) {
                        foreach($this->seller_fields() as $input) {
                            if($input['name'] == $key) {
                                $message[] = array(
                                    'text'  => sprintf(__('O campo %s é obrigatório e não pode estar vazio', 'whats-order'), strtolower($input['label'])),
                                    'class' => 'error'
                                );
                            }
                        }  
                    }
                }

                if(count(get_users('meta_value=' . $fields['whats'])) > 0 and !empty($fields['whats'])) {
                    $message[] = array(
                        'text'=> __('O número de WhatsApp informado já está cadastrado no sistema', 'whats-order'),
                        'class' => 'error'
                    );
                }

                if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL) and !empty($fields['email'])) {
                    $message[] = array(
                        'text'=> __('O e-mail informado é inválido', 'whats-order'),
                        'class' => 'error'
                    );
                }

                if(email_exists($fields['email'])) {
                    $message[] = array(
                        'text'=> __('O e-mail informado já está cadastrado no sistema', 'whats-order'),
                        'class' => 'error'
                    );
                }


                if(empty($message)) {
                    $userdata = array(
                        'user_login'    => rand(),
                        'first_name'    => $fields['fname'],
                        'last_name'     => $fields['lname'],
                        'user_email'    => $fields['email'],
                        'user_pass'     => md5(rand()),
                        'role'          => 'seller'
                    );
                     
                    $user_id = wp_insert_user( $userdata ) ;
                    add_user_meta( $user_id, 'whatsapp', $fields['whats'], true);
                     
                    if (is_wp_error( $user_id ) ) {
                        wp_die(__('Falha ao adicionar vendedor, o erro ocorreu com a função "wp_insert_user", entre em contato com o desenvolvedor para obter suporte', 'whats-order'));
                    }

                    unset($_POST);
                    header("Location: " . $this->url());
                    exit;
                }
               
            endif;
            
            ?>

            <?php if(!empty($message)) {
                foreach($message as $msg) { ?>
                    <div class="mt-1 custom-notice notice-<?= $msg['class'] ?>">
                        <p class="m-0"><?= $msg['text'] ?></p>
                    </div>
                <?php }
            }?>

            <h1 class="wp-heading-inline"><?php _e('Adicionar novo vendedor', 'whats-order'); ?></h1>
            <form action="" method="POST">
                <table class="form-table form-sellers">
                    <tbody>
                        <?php foreach($this->seller_fields() as $fields) : ?>
                            <tr class="form-field">
                                <th>
                                    <label for="<?= $fields['name'] ?>"><?= $fields['label'] ?></label>
                                </th>
                                <td>
                                    <input  
                                    name="<?= $fields['name'] ?>" 
                                    type="<?= $fields['type'] ? $fields['type'] : 'text' ?>" 
                                    id="<?= $fields['name'] ?>"
                                    <?= $_POST[$fields['name']] ? 'value="' . $_POST[$fields['name']] . '"' : false ?>
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" name="newuser" id="newuser" class="button button-primary" value="newuser">
                            <?php _e('Adicionar vendedor'); ?>
                    </button>
                </p>
            </form>
			<?php wp_die();
		}
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;

		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();
        $this->process_new_action();
        $this->process_edit_action();
        $this->process_conf_action();

		/*
		 * GET THE DATA!
		 * 
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our dummy data.
		 * 
		 * In a real-world situation, this is probably where you would want to 
		 * make your actual database query. Likewise, you will probably want to
		 * use any posted sort or pagination data to build a custom query instead, 
		 * as you'll then be able to use the returned query data immediately.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */
		$data = $this->SellerFields();

		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for 
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */
		usort( $data, array( $this, 'usort_reorder' ) );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Callback to allow sorting of example data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $b, $a ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'vendedor'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}
