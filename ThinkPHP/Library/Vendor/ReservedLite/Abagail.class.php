<?php

    function searchExamination($data)
    {

        import("Org.Nx.AjaxPage");

        $limitRows = 4;

        $map = array();

        $where = " a.status=1 and a.is_available=1";


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();


        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_category"] != 0) {

            $map["test_cat_id"] = $data["test_category"];

            $where .= " and a.test_cat_id= " . $data["test_category"];


        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        if ($data["test_name"] != "") {

            $datas["test_name"] = "%" . $data["test_name"] . "%";

            $map['test_name'] = array('like', $datas["test_name"]);

            $where .= " and a.test_name like " . "'%" . $data["test_name"] . "%'";
        }

        $map["status"] = 1;

        $map["is_available"] = 1;

        $testCategoryTable = $this->tablePrefix . "examination_category";

        $count = $this->where($map)->count();

        $p = new \AjaxPage($count, $limitRows, "test");

        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');

        $show = $p->show();

        $assign = array(

            "show" => $show
        );

        return $assign;

    }

?>