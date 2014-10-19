<?php
/**
 * Description of admin
 *
 * @author denganimax
 */
class admin extends TFController {
    public function index(){
        $view = new TFView('Adminsite/index.html');
        return $view;
    }
}
