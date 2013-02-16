<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* 404.php - Exceptino
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* This hanldes 404 errors
* Started on 2013-02-15
*************************************************************/

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {
 
    /**
     * Generate a Response for the 404 Exception.
     *
     * The user should be shown a nice 404 page.
     * 
     * @return Response
     */
    public function get_response()
    {
    	
        $content = View::factory('errors/404');
 
        // Remembering that `$this` is an instance of HTTP_Exception_404
        $content->message = URL::site(null,true).Request::current()->uri();
        
        $template = View::factory('main');
        $template->html_head = View::factory('html_head');
        $template->html_head->title = __('We cannot find that');
        $template->header = View::factory('header');
        $template->footer = View::factory('footer');
        $template->content = $content;
        
        //make messages roll up when done
        $template->html_head->messages_roll_up = true;
        //the name in the menu
        $template->header->menu_page = "error";
        $template->header->user = null;
        $template->html_head->script_files= array();
        $template->html_head->script_views= array();
        $template->html_head->styles= array();
        $template->html_head->styles['screen'] = 'media/css/style.css';
 
        
        
        $response = Response::factory()
            ->status(404)
            ->body($template->render());
 
        return $response;
    }
}