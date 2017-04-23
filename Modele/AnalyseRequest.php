<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Framework/ModeleJsonRPC.php';

class AnalyseRequest extends ModelejsonRPC {

    function __construct() {
        $this->selectCurrent('analyzeReq');
    }

    public function doAnalyze($request) {
        try {
            return $this->executerRequete('myCpl', $request);
        } catch (Exception $e) {
            return false;
        }
    }

}
