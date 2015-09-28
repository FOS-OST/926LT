<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;
use MongoDate;
use Phalcon\Mvc\View;
use Books\Backend\Models\LoginMember;
class DashboardController extends ControllerBase {
    /**
     *
     */
    public function indexAction() {
        $date = new MongoDate(time()-3600);
        $conditions = LoginMember::buildConditionsCreate($date);
        $online = LoginMember::count([$conditions]);
        $this->addViewVar('online', $online);
        
    }
}
