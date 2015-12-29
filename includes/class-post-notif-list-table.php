<?php

/**
 * An enhanced WP List Table class, distilled from Matt Van Andel's 
 *	(http://www.mattvanandel.com) extremely helpful Custom List Table Example 
 *	(https://wordpress.org/plugins/custom-list-table-example/).
 *					
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
 */

if ( ! class_exists( 'Post_Notif_WP_List_Table' ) ) {
		  
    // Include clone of WP core List Table (as of WP 4.1.1)
    require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-notif-wp-list-table.php' );
}

/**
 * An enhanced WP List Table class
 *
 * Defines the available actions, actual table data, as well as the methods to 
 *	generate a List Table with specified headers, sortable columns, prescribed 
 * 	pagination, and any defined single and bulk actions.
 *
 * @since		1.0.0
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
 * @author		Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_List_Table extends Post_Notif_WP_List_Table {

	/**
	 * The array of actions (both single and bulk) applicable to List Table items
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var     array	$available_actions_arr	Available actions.
	 */
	private $available_actions_arr;

	/**
	 * The array containing actual table data for display in List Table
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var     array	$table_data_arr	Actual table data.
	 */
	private $table_data_arr;
	
	/**
	 * The string containing clean $_SERVER['REQUEST_URI'] value
	 *
	 * @since	1.0.5
	 * @access	private
	 * @var     string	$clean_uri	$_SERVER['REQUEST_URI'] value.
	 */
	private $clean_uri;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	array	$class_settings_arr	Parent class defaults.
	 * @param	array	$table_data_arr	Actual table data.
	 */
	public function __construct( $class_settings_arr, $table_data_arr, $clean_uri ) {
				  
		global $status, $page;
                
		// Set parent defaults
		parent::__construct(
			array(
				'singular'  => $class_settings_arr['singular']
				,'plural'    => $class_settings_arr['plural']
				,'ajax'      => $class_settings_arr['ajax']
			)
		);

		$this->available_actions_arr = $class_settings_arr['available_actions_arr'];
		$this->table_data_arr = $table_data_arr;
		$this->clean_uri = $clean_uri;
      
	}

 	/**
	 * Display any non-checkbox column, with single action, if appropriate.
	 *
	 * @since	1.0.0
	 * @param 	array	$item	A singular item (one full row's worth of data).
	 * @param	string	$column_name	The name/slug of the column to be processed.
	 * @return	string	Column HTML for display
	 */
	public function column_default( $item, $column_name ) {
      		    			
		if ( $column_name == $this->available_actions_arr['actionable_column_name'] ) {
    	   		  
    		// This IS the column that MAY need a single action link
    		$actions = array();
    		foreach ( $this->available_actions_arr['actions'] as $single_action_arr_key => $single_action_arr_val ) {
    			if ( true == $single_action_arr_val['single_ok'] )  {
    					  
    				// If either there is NO single conditional field defined OR
    				//		there IS a single conditional field defined AND that
    				//		field's value is in the defined set of permissible values
    				if ( ( ! array_key_exists( 'single_conditional', $single_action_arr_val ) )   				
    					|| ( ( in_array( 'single_conditional', $single_action_arr_val ) ) 
    						&& ( in_array( $item[ $single_action_arr_val['single_conditional']['conditional_field'] ], $single_action_arr_val['single_conditional']['field_values'] ) ) 
    						) 
    					) {

    					// This column DOES need a single action link, for current action
						$action_url = sprintf( 
    						'%s&action=%s&%s=%s'
    						,$this->clean_uri
    						,$single_action_arr_key
    						,$this->_args['singular']
    						,$item['id']
    					);
    					
    					// Generate nonce
    					$action_url = wp_nonce_url( $action_url, 'post_notif_' . $single_action_arr_key
    						. '_' . $item['id'] );
    					
    					// Finalize URL
    					$actions[ $single_action_arr_key ] = '<a href="' . $action_url . '">' . $single_action_arr_val['label'] . '</a>';
    				}
    			}
    		}
    		
    		return sprintf( '%1$s%2$s', $item[ $column_name ], $this->row_actions( $actions ) );
    	}
    	else {

			// This is a normal column, no action link necessary		
			return $item[ $column_name ];
    	}
    	
    }

 	/**
	 * Display checkbox column (for use by bulk actions).
	 *
	 * @since	1.0.0
	 * @param 	array	$item	A singular item (one full row's worth of data).
	 * @return	string	Checkbox column HTML for display
	 */
	public function column_cb( $item ) {
   		  
		return sprintf(
			'<input type="checkbox" name="chkKey_%1$s" value="%2$s" />'
			,$item['id']
			,$item['id']
		);
      
	}

 	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since	1.0.0
	 * @access	protected
	 * @return	array	Set of available bulk actions
	 */
	protected function get_bulk_actions() {

		$actions = array();
    	if ( is_array( $this->available_actions_arr ) ) {
    		foreach( $this->available_actions_arr['actions'] as $single_action_arr_key => $single_action_arr_val ) {

    			// Iterate through available actions, defined as "bulk_ok", adding them to actions array
    			if ( true == $single_action_arr_val['bulk_ok'] )  {
    				$actions[ $single_action_arr_key ] = $single_action_arr_val['label'];
    			}
    		}
    	}
    	
    	return $actions;
    	
    }

 	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since	1.0.0
	 */
	public function prepare_items() {       
        
		// Define column headers (displayed, hidden, and sortable)
		$columns = $this->table_data_arr['columns_arr'];
		$hidden = $this->table_data_arr['hidden_columns_arr'];
		$sortable = $this->table_data_arr['sortable_columns_arr'];
                
		// Build array of column headers
		$this->_column_headers = array( $columns, $hidden, $sortable );                       
        
		// Define dataset
		$data = $this->table_data_arr['table_contents_arr'];
                                                
		// Handle pagination
		$total_items = count( $data );
      
		// Set per page
		$per_page = $this->table_data_arr['rows_per_page'];
      
		// Set current page
		$current_page = $this->get_pagenum();
      
		// Define pagination array
		$pagination_arr = array(
			'total_items' => $total_items
		);
      
		if ( 0 != $per_page ) {
        
			// Trim up dataset to pass Post_Notif_WP_List_Table class only the current page
			$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
			$pagination_arr['per_page'] = $per_page;	  
			$pagination_arr['total_pages'] = ceil( $total_items / $per_page );
		}
		else {
      	
			// Display ALL rows on a single page (effectively NO pagination)
			$pagination_arr['total_pages'] = 1;
		}
		$this->set_pagination_args( $pagination_arr );
         
		$this->items = $data;
   	
	}
    
 	/**
	 * Message to be displayed when there are no items
	 *
	 * @since	1.0.0
	 */
	public function no_items() {

		printf( __( 'No %s found.', 'post-notif' ), $this->_args['plural'] );
		
	}
	
}
