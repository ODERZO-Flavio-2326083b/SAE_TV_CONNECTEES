<?php

use controllers\InformationController;

add_action('widgets_init', 'information_register_widget');
function information_register_widget() {
    register_widget( 'WidgetInformation');
}

/**
 * Class WidgetInformation
 *
 * Widget for information
 */
class WidgetInformation extends WP_Widget
{
	/**
	 * WidgetInformation constructor.
	 */
    public function __construct()
    {
        parent::__construct(
        // widget ID
            'information_widget',
            // widget name
            __('InformationController widget', ' teleconnecteeamu_widget_domain'),
            // widget description
            array( 'description' => __( 'Widget qui affiche des informations', 'teleconnecteeamu_widget_domain' ), )
        );
    }

	/**
	 * Function of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
    public function widget($args, $instance)
    {
        $view = new InformationController();
        $view->informationMain();
    }

	/**
	 * @param array $instance
	 *
	 * @return string|void
	 */
    public function form($instance)
    {
        if (isset($instance['title'])) {
	        $title = $instance['title'];
        } else {
	        $title = __('Default Title', 'teleconnectee_widget_domain');
        }

        echo '
        <p>
            <label for="'.$this->get_field_id('title').'">'; _e('Title:'); echo '</label>
            <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" />
        </p>';
    }

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (! empty( $new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}