<?php
function counter($what)
{
    static $_counter;
    if (isset($_counter[$what]))
    {
        return $_counter[$what];
    }
    $fa = fa_instance();
    /**
     * Load account model
     */
    $fa->load->model($what);
    $model = $fa->model->$what;
    $_counter[$what] = $model->count();
    return $_counter[$what];
}