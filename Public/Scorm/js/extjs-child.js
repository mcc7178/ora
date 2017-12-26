LoadGridView = function (contrainerID, headList, JsonData) {
    if (contrainerID == null) return;
    if (contrainerID.length == 0) return;

    var _borderWidth = 1;
    var _width = 720;
    var _height = 100;
    var _headTextAlign = 'center';
    var _OperateWidth = 0;

    var LoadGridViewObj = this;

    //设置边框线宽度
    LoadGridView.prototype.setBorderWidth = function (borderWidth) {
        _borderWidth = borderWidth;
        return LoadGridViewObj;
    }

    LoadGridView.prototype.setGridViewWidth = function (width) {
        _width = width;
        return LoadGridViewObj;
    }

    LoadGridView.prototype.setGridViewHeight = function (height) {
        _height = height;
        return LoadGridViewObj;
    }

    LoadGridView.prototype.setHeadTextAlign = function (headTextAlign) {
        _headTextAlign = headTextAlign;
        return LoadGridViewObj;
    }

    LoadGridView.prototype.setOperateWidth = function (OperateWidth) {
        _OperateWidth = OperateWidth;
        return LoadGridViewObj;
    }

    var _pagesize = 10;
    //设置每页显示多少条数据
    LoadGridView.prototype.setPageSize = function (pagesize) {
        _pagesize = pagesize;
        return LoadGridViewObj;
    }

    var _recordcounts = 0;
    //设置数据总条数
    LoadGridView.prototype.setRecordCounts = function (recordcounts) {
        _recordcounts = recordcounts;
        return LoadGridViewObj;
    }

    var _noEditButton = true;  //是否开启编辑按钮
    var _noDeleteButton = true;  //是否开启删除按钮

    LoadGridView.prototype.noEditButton = function (noEditButton) {
        _noEditButton = noEditButton;
        return LoadGridViewObj;
    }

    LoadGridView.prototype.noDeleteButton = function (noDeleteButton) {
        _noDeleteButton = noDeleteButton;
        return LoadGridViewObj;
    }

    var _editLogOfImgPath = '/Images/TrainManager/modify_bttn.jpg';
    var _deleteLogOfImgPath = '/Images/TrainManager/delete_bttn.jpg';

    var _borderColor = '#CBCBCB';
    LoadGridView.prototype.setBorderColor = function (borderColor) {
        _borderColor = borderColor;
        return LoadGridViewObj;
    }

    //编辑按钮图片
    LoadGridView.prototype.setEditLogOfImgPath = function (editLogOfImgPath) {
        _editLogOfImgPath = editLogOfImgPath;
        return LoadGridViewObj;
    }

    //删除按钮图片
    LoadGridView.prototype.setDeleteLogOfImgPath = function (deleteLogOfImgPath) {
        _deleteLogOfImgPath = deleteLogOfImgPath;
        return LoadGridViewObj;
    }

    //Summary 单选按钮(radioButton)被选中时发生
    //Paramater rowObject:
    //第一个参数rowObject包含方法:checked(bool),GetJsonString(),GetFieldValue(fieldName, columnIndex),GetRowIndex(),SetRowCss(cssValue)
    //属性: controlType, checkBox, radioButton
    //Paramater rowData:
    //当前行所包含的数据集
    //Paramater rowIndex: 当前行序号
    //Paramater 布尔值, true为选中状态
    var radioSelectedCallback = function (rowObject, rowData, rowIndex, checked) { };
    LoadGridView.prototype.setRadioSelectedCallback = function (radioSelectedCallback_function) {
        radioSelectedCallback = radioSelectedCallback_function;
        return LoadGridViewObj;
    }

    //Summary 复选框(checkBox)点击全选时发生
    //Paramater rowsObject: 
    //包含方法: GetJsonString()
    var checkSelectedAllCallback = function (rowsObject, rowsData, checked) { };
    LoadGridView.prototype.setCheckSelectedAllCallback = function (checkSelectedAllCallback_function) {
        checkSelectedAllCallback = checkSelectedAllCallback_function;
        return LoadGridViewObj;
    }

    //Summary 复选框(checkBox)单击某项时发生
    var checkSelectedCallback = function (rowObject, rowData, rowIndex, checked) { };
    LoadGridView.prototype.setCheckSelectedCallback = function (checkSelectedCallback_function) {
        checkSelectedCallback = checkSelectedCallback_function;
        return LoadGridViewObj;
    }

    //点击编辑按钮时发生
    var editButtonClick = function (rowObject, rowData, rowIndex) { };
    LoadGridView.prototype.setEditButtonClick = function (editButtonClick_function) {
        editButtonClick = editButtonClick_function;
        return LoadGridViewObj;
    }

    //点击删除按钮时发生
    var deleteButtonClick = function (rowObject, rowData, rowIndex) { };
    LoadGridView.prototype.setDeleteButtonClick = function (deleteButtonClick_function) {
        deleteButtonClick = deleteButtonClick_function;
        return LoadGridViewObj;
    }

    //加载行数据行时触发
    var rowIteratorCallback = function (rowObject, rowData, rowIndex) { };
    LoadGridView.prototype.setRowIteratorCallback = function (rowIteratorCallback_function) {
        rowIteratorCallback = rowIteratorCallback_function;
        return LoadGridViewObj;
    }

    var rowMouseOverCallback = function (rowObject, rowIndex) { };  //当鼠标进入数据行时触发
    LoadGridView.prototype.setRowMouseOverCallback = function (rowMouseOverCallback_function) {
        rowMouseOverCallback = rowMouseOverCallback_function;
        return LoadGridViewObj;
    }

    var rowMouseOutCallback = function (rowObject, rowIndex) { };  //当鼠标离开数据行时触发
    LoadGridView.prototype.setRowMouseOutCallback = function (rowMouseOutCallback_function) {
        rowMouseOutCallback = rowMouseOutCallback_function;
        return LoadGridViewObj;
    }

    //参数:GoPage_Object 包含方法：GoPage(jsonData), GetJsonDataOfOld(), GetJsonStringOfOld()
    var _gotoPageBeforeCallback = function (GoPage_Object, page_Size, page_Index) { };
    LoadGridView.prototype.setGotoPageBeforeCallback = function (gotoPageBeforeCallback_function) {
        _gotoPageBeforeCallback = gotoPageBeforeCallback_function;
        return LoadGridViewObj;
    }

    var _gotoPageAfterCallback = function (page_Index, page_Counts, json_data) { };
    //翻页之后执行
    LoadGridView.prototype.setGotoPageAfterCallback = function (gotoPageAfterCallback_function) {
        _gotoPageAfterCallback = gotoPageAfterCallback_function;
        return LoadGridViewObj;
    }

    var dataStore = [{ name: '小王', age: 20, sex: '男', mobile: '13312540115', address: '广东省1' },
    { name: '洋洋', age: 18, sex: '女', mobile: '13312540115', address: '广东省2' },
    { name: '李三', age: 18, sex: '男', mobile: '13312540115', address: '广东省3' },
    { name: '张五', age: 18, sex: '男', mobile: '13312540115', address: '广东省4' },
    { name: '李美', age: 28, sex: '女', mobile: '13312540115', address: '广东省5' }];

    var Columns = [
        { headText: '选择', field: '', width: 50, height: 50, isOrderBy: true, textAlign: 'center', dataTextAlign: 'center', Css: '', checkBox: false, radioButton: true },
        { headText: '姓名', field: 'name', width: 80, height: 30, isOrderBy: true, textAlign: 'center', dataTextAlign: 'center', Css: '' },
        { headText: '性别', field: 'sex', width: 80, height: 30, isOrderBy: true, textAlign: 'center', dataTextAlign: 'center', Css: '' },
        { headText: '年龄', field: 'age', width: 80, height: 30, isOrderBy: true, textAlign: 'center', dataTextAlign: 'center', Css: '' },
        { headText: '手机', field: 'mobile', width: 120, height: 30, isOrderBy: true, textAlign: 'center', dataTextAlign: 'center', Css: '' },
        { headText: '地址', field: 'address', width: 220, height: 30, isOrderBy: true, textAlign: 'center', dataTextAlign: 'left', Css: '' }];
        
    LoadGridView.prototype.djExtendjsGridView = function (contrainerID1, headList1, JsonData1) {
        if (contrainerID1 != null) {
            if (contrainerID1.toString().length > 0) {
                contrainerID = contrainerID1;
            }
        }

        if (headList1 != null) {
            headList = headList1;
        }

        if (JsonData1 != null) {
            if (JsonData1.toString().length == 0) {                
                JsonData = null;
                dataStore = null;
            }
            else {
                JsonData = JsonData1;
            }
        }
        
        if (headList != null) {
            Columns = headList;
        }

        if (JsonData != null) {
            dataStore = JsonData;
        }

        $('#' + contrainerID).djExtendjsGridView({
            borderWidth: _borderWidth,
            borderColor: _borderColor,
            backgroundColor: '#ffffff',
            data: dataStore,
            indexNumber: true,
            columns: Columns,
            headCss: 'color:#023A82;', //background-color:#D8E9FF;color:#023A82;
            headTextAlign: _headTextAlign,
            dataCss: '',
            dataTextAlign: 'center',
            Text:
                {
                    fontSize: 12,
                    fontFamily: '宋体',
                    fontColor: '#000000',
                    backgroundColor: 'white',
                    align: 'center' //left center right
                },
            width: _width,
            height: _height,
            OperateWidth: _OperateWidth,
            pagesize: 10,
            gopageWidth: 0, //翻页条的宽度
            gopageHeight: 0, //翻页条的高度
            gopageAlign: 'center',////翻页条的位置
            gopageMarginTop: 0, //翻页条的顶部间距
            pagesize: _pagesize, //
            recordcounts: _recordcounts,
            pagefirstImgPath: '/Images/page-first.png', //翻面按钮(首页)图标
            pagepreviousImgPath: '/Images/page-prev.png', //翻面按钮(上一页)图标
            pagenextImgPath: '/Images/page-next.png', //翻面按钮(下一页)图标
            pagelastImgPath: '/Images/page-last.png', //翻面按钮(尾页)图标
            Css: '',
            cellRightTopHistoryImgPath: '/Images/TrainManager/history.png', //单元格右上角历史恢复小图标
            radioDefaultImgPath: '',   //radioButton末选择时的背景图片 /images/radiobutton_00.png
            radioSelectedImgPath: '',  //radioButton选中状态下的背景图片
            checkDefaultImgPath: '/Images/TrainManager/checkbox_00.png',   //checkBox末选择时的背景图片 /images/checkbox_00.png
            checkSelectedImgPath: '/Images/TrainManager/checkbox_01.png',   //checkBox选中状态下的背景图片
            noEditButton: _noEditButton,  //是否开启编辑按钮
            noDeleteButton: _noDeleteButton,  //是否开启删除按钮
            noEditCell: false, //是否开启编辑单元格
            noHistory: false, //是否开启单元格修改历史记录
            orderDefaultImgPath: '/Images/TrainManager/orderLog01.png', //images/orderLog01.png
            orderAscImgPath: '/Images/TrainManager/orderLog02.png',
            orderDescImgPath: '/Images/TrainManager/orderLog03.png',
            editLogOfImgPath: _editLogOfImgPath, //'/Images/TrainManager/modify_bttn.jpg',
            deleteLogOfImgPath: _deleteLogOfImgPath, //'/Images/TrainManager/delete_bttn.jpg',
            rowMouseOverForeColor: '',
            rowMouseOutForeColor: '',
            rowMouseOverBgColor: '',
            rowMouseOutBgColor: '',
            gotoPageBeforeCallback: _gotoPageBeforeCallback, //function (GoPage_Object, page_Size, page_Index) { }, //翻页前执行
            gotoPageAfterCallback: _gotoPageAfterCallback, //function (page_Index, page_Counts, json_data) { }, //翻页之后执行
            radioSelectedCallback: radioSelectedCallback,
            checkSelectedAllCallback: checkSelectedAllCallback,
            checkSelectedCallback: checkSelectedCallback,
            //第一个参数rowObject包含方法:checked(bool),GetJsonString(),GetFieldValue(fieldName, columnIndex),GetRowIndex(),SetRowCss(cssValue)
            editButtonClick: editButtonClick,
            deleteButtonClick: deleteButtonClick,
            rowIteratorCallback: rowIteratorCallback,
            rowMouseOverCallback: rowMouseOverCallback,  //当鼠标进入数据行时触发
            rowMouseOutCallback: rowMouseOutCallback,  //当鼠标离开数据行时触发
            cellFocusCallback: function (cellObject, cellData) { },
            cellBlurCallback: function (cellObject, cellData) { },
            cellValueChangeCallback: function (cellObject, cellOldData, cellNewData) { }
        });
    }

}

//event1 文本框上级div的ID
var ComboxOptionChange = { 'event1': function (_text, _value) { } };

initialCombox = function () {
    setTimeout(executeInitial, 200);
    function executeInitial(readonly) {
        var parentObj = null;
        var cmbListObj = null;
        var cmbListborderWidth = 0;
        var cmbInputObj = null;
        var inputBorderWidth = 0;
        var n1 = 0;
        var w1 = 0;
        var w2 = 0;
        var comboxindex = 0;
        var readonly1 = null;
        var isBlock = false;

        $('.cm_info_right_condition_input_sign').each(function () {
            parentObj = $(this).parent();

            w1 = $(parentObj).css('width');
            w1 = w1.toString().replace('px', '');
            w1 = parseInt(w1);

            w2 = $(this).css('width');
            w2 = w2.toString().replace('px', '');
            w2 = parseInt(w2);

            cmbListObj = null;
            n1 = 0;
            $(parentObj).children('input').each(function () {
                if (n1 == 0) {
                    cmbInputObj = this;  //获取文本框对象
                }
                n1++;
            });
                        
            if (cmbInputObj != null) {
                inputBorderWidth = $(cmbInputObj).css('border-left-width');
                inputBorderWidth = inputBorderWidth.toString().replace('px', '');
                inputBorderWidth = parseInt(inputBorderWidth);
                $(cmbInputObj).css('width', (w1 - w2 - (inputBorderWidth * 2) - 5) + 'px');
                if (readonly != null) {
                    readonly = readonly.toString().toLowerCase() == 'true';
                    if (readonly) {
                        $(cmbInputObj).attr('readonly', 'true');
                    }
                    else {
                        $(cmbInputObj).attr('readonly', null);
                    }
                }

                readonly1 = $(cmbInputObj).attr('readonly');
                if (readonly1 != null) {
                    if (readonly1.toString().toLowerCase() == 'true') {
                        $(cmbInputObj).css('cursor', 'pointer');
                        try {
                            $(cmbInputObj).unbind('mousedown');
                        } catch (e) {

                        }
                        $(cmbInputObj).bind('mousedown', function () {
                            var parentObj = $(this).parent();
                            var cmbListObj = null;

                            n1 = 0;
                            $(parentObj).children('div').each(function () {
                                if (n1 == 2) {
                                    cmbListObj = this;  //获取数据下拉列表对象
                                }
                                n1++;
                            });

                            var currCss = $(cmbListObj).attr('class') + "";
                            var arr = currCss.split(" ");
                            
                            if (arr[1] == "cm_info_right_condition_displayNone") {
                                currCss = arr[0] + " cm_info_right_condition_displayBlock";
                                isBlock = true;
                            }
                            else {
                                currCss = arr[0] + " cm_info_right_condition_displayNone";
                                isBlock = false;
                            }
                            //$(cmbListObj).attr({ 'class': currCss });
                            hiddrenList(cmbListObj, isBlock);
                        });
                    }

                }
            }

            n1 = 0;
            $(parentObj).children('div').each(function () {
                if (n1 == 2) {
                    cmbListObj = this;  //获取数据下拉列表对象
                }
                n1++;
            });

            cmbListborderWidth = 0;
            $(cmbListObj).children('div').each(function () {
                cmbListborderWidth = $(cmbListObj).css('border-left-width');
                cmbListborderWidth = cmbListborderWidth.toString().replace('px', '');
                cmbListborderWidth = parseInt(cmbListborderWidth);
                $(this).css('width', (w1 - (cmbListborderWidth * 2) - 10) + 'px');
            });

            if (cmbListObj != null) {
                $(cmbListObj).css({ 'width': (w1 - (cmbListborderWidth * 2)) + 'px' });
            }

            //$(parentObj).load();

            if (cmbListObj != null && cmbInputObj != null) {
                try {
                    $(this).unbind('mousedown');
                } catch (e) {

                }
                $(this).bind('mousedown', function (o) {
                    var parentObj = $(this).parent();
                    var cmbListObj = null;

                    n1 = 0;
                    $(parentObj).children('div').each(function () {
                        if (n1 == 2) {
                            cmbListObj = this;  //获取数据下拉列表对象
                        }
                        n1++;
                    });

                    var currCss = $(cmbListObj).attr('class') + "";
                    var arr = currCss.split(" ");
                    if (arr[1] == "cm_info_right_condition_displayNone") {
                        currCss = arr[0] + " cm_info_right_condition_displayBlock";
                        isBlock = true;
                    }
                    else {
                        currCss = arr[0] + " cm_info_right_condition_displayNone";
                        isBlock = false;
                    }
                    //$(cmbListObj).attr({ 'class': currCss });
                    hiddrenList(cmbListObj, isBlock);
                });

                n1 = 0;
                $(cmbListObj).children('div').each(function (e) {
                    try {
                        $(this).unbind('mousedown');
                    } catch (e) {

                    }
                    $(this).attr('comboxindex', comboxindex);
                    $(this).bind('mousedown', function (o) {
                        var parentObj = $(this).parent().parent();
                        var parentID = null;
                        var cmbListObj = null;
                        var cmbInputObj = null;
                        var n1 = 0;
                        var comboxindex = $(this).attr('comboxindex');
                        comboxindex = parseInt(comboxindex);
                        
                        parentID = $(parentObj).attr('id');
                        parentID = parentID == null ? '' : parentID;
                        parentID = parentID.toString().length == 0 ? null : parentID;

                        n1 = 0;
                        $(parentObj).children('input').each(function () {
                            if (n1 == 0) {
                                cmbInputObj = this;  //获取文本框对象
                            }
                            n1++;
                        });

                        n1 = 0;
                        $(parentObj).children('div').each(function () {
                            if (n1 == 2) {
                                cmbListObj = this;  //获取数据下拉列表对象
                            }
                            n1++;
                        });

                        var txt = $(this).text();
                        var value1 = $(this).attr('value');
                        txt = txt == null ? "" : txt;
                        $(cmbInputObj).attr({ 'value': txt });

                        if (ComboxOptionChange != null) {
                            var eventObj = getComboxEvent(parentID);
                            if (eventObj == null) {
                                eventObj = getComboxEvent(null, comboxindex);
                            }

                            if (eventObj != null) {
                                eventObj(txt, value1);
                            }
                        }
                        var currCss = $(cmbListObj).attr('class') + "";
                        var arr = currCss.split(" ");
                        currCss = arr[0] + " cm_info_right_condition_displayNone";
                        $(cmbListObj).attr({ 'class': currCss });
                    });
                    n1++;
                });
            }

            comboxindex++;
        });
    }

    function getComboxEvent(eventName, eventIndex) {
        eventName = eventName == null ? '' : eventName;

        eventIndex = eventIndex == null ? '' : eventIndex;
        eventIndex = eventIndex.toString().length == 0 ? -1 : eventIndex;
        eventIndex = isNaN(eventIndex) ? -1 : eventIndex;
        eventIndex = parseInt(eventIndex);

        var eventObj = null;

        if (ComboxOptionChange == null) return eventObj;
        
        if (eventName.toString().length > 0) {
            eventName = eventName.toString().toLowerCase();
            for (var en in ComboxOptionChange) {
                if (en.toString().toLowerCase() == eventName) {
                    eventObj = ComboxOptionChange[en];
                    break;
                }
            }
        }
        else if (eventIndex >= 0) {
            var n = 0;
            for (var en in ComboxOptionChange) {
                if (n == eventIndex) {
                    eventObj = ComboxOptionChange[en];
                    break;
                }
                n++;
            }
        }

        return eventObj;
    }

    function hiddrenList(listObj, isBlock) {
        var obj1 = null;
        $('.cm_info_right_conditionNowPageList').each(function () {
            $(this).attr('class', 'cm_info_right_conditionNowPageList cm_info_right_condition_displayNone');
        });

        if (isBlock) {
            $(listObj).attr('class', 'cm_info_right_conditionNowPageList cm_info_right_condition_displayBlock');
        }
    }
}

//callBack_function 有两个参数: x, y
InitialTouYing = function (DialogObj, contrainerObj, leftX, topY, IdArrayOfProhibitMove, callBack_function) {
    if (contrainerObj != null) {        
        DialogObj = getChildObject(contrainerObj, 0);
    }
    if (DialogObj == null) return;
        
    var x = 0;
    var y = 0;
    var x0 = 0;
    var y0 = 0;
    var x1 = 0, y1 = 0;
    var mbool = false;
    var w = 0;
    var h = 0;
    var w1 = $(DialogObj).css('width');
    var h1 = $(DialogObj).css('height');
    var w2 = 0;
    var h2 = 0;
    w1 = w1.toString().replace('px', '');
    h1 = h1.toString().replace('px', '');
    
    if (isNaN(h1) == false) {
        var contrainerObj = $(DialogObj).parent();

        var display = $(contrainerObj).css('display');
        display = display == null ? 'none' : display;
        display = display.length == 0 ? 'none' : display;
        if (display == 'none') {            
            $(contrainerObj).fadeIn(600);
        }
        
        var ty_right_contrainer_height = getChildObject(contrainerObj, 1);
        $(ty_right_contrainer_height).css('height', h1 + 'px');

        h2 = parseInt(h1) - 30 + 2;
        var ty_right_height = getChildObject(ty_right_contrainer_height, 1);
        $(ty_right_height).css('height', h2 + 'px');
        w2 = parseInt(w1) + 19;

        var ty_bottom_contrainer_width = getChildObject(contrainerObj, 2);
        $(ty_bottom_contrainer_width).css('width', w2 + 'px');
        w2 = parseInt(w1) - 35;
        var ty_bottom_width = getChildObject(ty_bottom_contrainer_width, 1);
        $(ty_bottom_width).css('width', w2 + 'px');
        
        $(DialogObj).css({ 'border-width': '1px', 'border-style': 'solid', 'border-color': '#cbcbcb' });

        w = $(window).width();
        h = $(window).height();

        w2 = parseInt(w1) + 19 + 2;
        h2 = parseInt(h1) + 19 + 2;
        $(contrainerObj).css({ 'width': w2 + 'px', 'height': h2 + 'px' });

        w2 = $(contrainerObj).css('width');
        w2 = w2.toString().replace('px', '');
        x = (parseInt(w) - parseInt(w2)) / 2 + 19;
        y = (parseInt(h) - parseInt(h2)) / 2 - 19;

        leftX = leftX == null ? -1 : leftX;
        leftX = leftX.toString().length == 0 ? -1 : leftX;
        topY = topY == null ? -1 : topY;
        topY = topY.toString().length == 0 ? -1 : topY;

        x = leftX != -1 ? leftX : x;
        y = topY != -1 ? topY : y;
        x0 = x;
        y0 = y;
        $(contrainerObj).css({ 'left': x, 'top': y });

        try {
            $(contrainerObj).unbind('mousedown');
        } catch (e) {

        }

        try {
            $(contrainerObj).unbind('mousemove');
        } catch (e) {

        }

        try {
            $(contrainerObj).unbind('mouseup');
        } catch (e) {

        }
        
        var outBool = true;
        $(DialogObj).find('input').each(function () {
            try {
                $(this).unbind('mouseover');
            } catch (e) {

            }

            try {
                $(this).unbind('mouseout');
            } catch (e) {

            }
            $(this).bind('mouseover', function () {
                outBool = false;
                mbool = false;
                x = 0;
                y = 0;
                x1 = 0;
                y1 = 0;
                $(this).css({ 'cursor': 'default' });
            });

            $(this).bind('mouseout', function () {
                outBool = true;                
            });
        });

        if (IdArrayOfProhibitMove != null) {
            var len = IdArrayOfProhibitMove.length;
            for (var i = 0; i < len; i++) {
                try {
                    $('#' + IdArrayOfProhibitMove[i]).unbind('mouseover');
                } catch (e) {

                }

                try {
                    $('#' + IdArrayOfProhibitMove[i]).unbind('mouseout');
                } catch (e) {

                }
                $('#' + IdArrayOfProhibitMove[i]).bind('mouseover', function () {
                    outBool = false;
                    mbool = false;
                    x = 0;
                    y = 0;
                    x1 = 0;
                    y1 = 0;
                    //$(this).css({ 'cursor': 'default' });
                });

                $('#' + IdArrayOfProhibitMove[i]).bind('mouseout', function () {
                    outBool = true;
                });
            }
        }

        var thisX = 0;
        var thisY = 0;
        var currentX = 0;
        var currentY = 0;        
        x = 0;
        y = 0;
        $(contrainerObj).bind('mousedown', function () {
            if (outBool == false) return;
            mbool = true;
            x = event.x;
            y = event.y;
            
            thisX = $(this).css('left');
            thisY = $(this).css('top');

            thisX = thisX.toString().replace('px', '');
            thisY = thisY.toString().replace('px', '');

            thisX = parseInt(thisX);
            thisY = parseInt(thisY);
            $(this).css({ 'cursor': 'move' });
        });

        $(contrainerObj).bind('mouseup', function () {
            if (outBool == false) return;
            mbool = false;
            x = 0;
            y = 0;
            x1 = 0;
            y1 = 0;
            $(this).css({ 'cursor': 'default' });
            if (callBack_function != null) {
                callBack_function(currentX, currentY);
            }
        });

        $(contrainerObj).bind('mousemove', function () {
            if (outBool == false) return;
            if (mbool) {
                x1 = event.x;
                y1 = event.y;
                currentX = x1 - x + thisX;
                currentY = y1 - y + thisY;

                var msie = "";
                var version = "";
                for (var e in $.browser) {
                    if (e.toString().toLowerCase() == 'msie') {
                        msie = "msie";
                    }
                    else if (e.toString().toLowerCase() == 'version') {
                        version = $.browser.version;
                    }
                }

                if (version.toString() != '8.0') {
                    if ($(this).get(0).setCapture) {
                        $(this).get(0).setCapture();
                    }
                }
                
                $(this).css({ 'left': currentX + 'px', 'top': currentY + 'px' });
            }
        });        
    }
    
    if (callBack_function != null) {
        callBack_function(x0, y0);
    }
}

function getChildObject(obj, index, tagName) {
    var n = 0;
    var obj1 = null;
    tagName = tagName == null ? 'div' : tagName;
    tagName = tagName.toString().length == 0 ? 'div' : tagName;
    $(obj).children(tagName).each(function () {
        if (n == index) {
            obj1 = $(this);
        }
        n++;
    });
    return obj1;
}

DataOperate = function (contrainerId) {
    DataOperate.ContrainerId = contrainerId;
    var _fieldSign = '';
    var thisObj = this;

    DataOperate.prototype.setFieldSign = function (fieldSign) {
        _fieldSign = fieldSign;
        return thisObj;
    }

    DataOperate.prototype.GetDataString = function (contrainerId1, tagName) {
        var dt = getJsonString(contrainerId1, tagName);
        return dt;
    }

    DataOperate.prototype.GetData = function (contrainerId1, tagName) {
        var dt = getJsonString(contrainerId1, tagName);
        try {
            dt = eval("(" + dt + ")");
        } catch (e) {

        }
        return dt;
    }

    DataOperate.prototype.GetDataPara = function (contrainerId1, tagName) {
        var ContrainerId0 = DataOperate.ContrainerId;
        if (contrainerId1 != null) {
            if (contrainerId1.toString().length > 0) {
                ContrainerId0 = contrainerId1;
            }
        }

        if (ContrainerId0 == null) return;
        if (ContrainerId0.toString().length == 0) return;

        _fieldSign = _fieldSign == null ? 'field' : _fieldSign;
        _fieldSign = _fieldSign.toString().length == 0 ? 'field' : _fieldSign;

        tagName = tagName == null ? 'input' : tagName;
        tagName = tagName.toString().length == 0 ? 'input' : tagName;

        var myfield = "";
        var fvalue = '';
        var dt = '';
        $('#' + ContrainerId0).find(tagName).each(function () {
            myfield = $(this).attr(_fieldSign);
            myfield = myfield == null ? '' : myfield;
            myfield = myfield.toString().length == 0 ? null : myfield;
            if (myfield != null) {
                if (tagName.toString().toLowerCase() == 'input') {
                    fvalue = $(this).attr('value');
                }

                if (fvalue.toString().length == 0) {
                    dt += '[' + myfield + ':\'' + fvalue + '\']';
                }
                else {
                    if (isNaN(fvalue)) {
                        dt += '[' + myfield + ':\'' + fvalue + '\']';
                    }
                    else {
                        dt += '[' + myfield + ':' + fvalue + ']';
                    }
                }
            }

        });

        if (dt != null) {
            dt = "{@para:" + dt + "}";
        }

        return dt;
    }

    DataOperate.prototype.LoadData = function (contrainerId1, jsonData, attrSign) {
        var ContrainerId0 = DataOperate.ContrainerId;
        if (contrainerId1 != null) {
            if (contrainerId1.toString().length > 0) {
                ContrainerId0 = contrainerId1;
            }
        }

        if (ContrainerId0 == null) return;
        if (ContrainerId0.toString().length == 0) return;

        var testArr = null;
        if (jsonData != null) {
            try {
                testArr = jsonData[0];
            } catch (e) {

            }

            if (testArr == null) {
                testArr = new Array();
                testArr[0] = jsonData;
                jsonData = testArr;
            }
        }

        if (attrSign != null) {
            if (attrSign.toString().length > 0) {
                _fieldSign = attrSign;
            }
        }

        _fieldSign = _fieldSign == null ? 'field' : _fieldSign;
        _fieldSign = _fieldSign.toString().length == 0 ? 'field' : _fieldSign;
        var field = '';
        var tag_name = '';
        var ctype = '';
        var Id = "";
        var labelObj = null;
        var labelText = "";
        var keyValue = '';
        var css1 = $('#' + ContrainerId0).attr('class');

        $('#' + ContrainerId0).find('input').each(function () {
            field = $(this).attr(_fieldSign);
            field = field == null ? '' : field;
            field = field.toString().length == 0 ? null : field;
            if (field != null) {
                field = field.toString().toLowerCase();
                keyValue = "";
                tag_name = $(this)[0].tagName.toString().toLowerCase();
                if (jsonData != null) {
                    for (var key in jsonData[0]) {
                        if (key.toString().toLowerCase() == field) {                            
                            keyValue = jsonData[0][key];
                            break;
                        }
                    }
                }
                
                if (tag_name.length > 0) {
                    if (tag_name == 'input') {
                        ctype = $(this).attr('type');
                        ctype = ctype == null ? '' : ctype;
                        ctype = ctype.toString().length == 0 ? 'text' : ctype;
                        ctype = ctype.toString().toLowerCase();
                        if (ctype == 'text' || ctype == 'password') {                            
                            $(this).val(keyValue);
                        }
                        else if (ctype == 'radio') {
                            Id = $(this).attr('id');
                            labelObj = null;
                            labelObj = $("label[for='" + Id + "']");
                            if (labelObj != null) {
                                labelText = $(labelObj).text();
                                keyValue = $.trim(keyValue);
                                if (labelText == keyValue) {
                                    $(this).attr('checked', true);
                                }
                            }
                        }
                        else if (ctype == 'checkbox') {

                        }
                    }
                    else if (tag_name == 'select') {
                        selectedTextObject(this, keyValue);
                        selectedValueObject(this, keyValue);
                    }
                    else {
                        $(this).text(keyValue);
                    }
                }
            }
        });
    }

    //标签格式{#fieldName}    
    DataOperate.prototype.IteratorForDataSign = function (HtmlOfDataSign, jsonData, connectSign) {
        if (HtmlOfDataSign == null) return;
        if (HtmlOfDataSign.toString().length == 0) return;
        if (jsonData == null) return;
        if (jsonData.length == 0) return;

        connectSign = connectSign == null ? '\r\n' : connectSign;

        var rg = new RegExp('\{\#(((?!\}).)+)\}');        
        var txt = null;        
        var len = jsonData.length;
        var html1 = "";
        for (var i = 0; i < len; i++) {
            html1 = HtmlOfDataSign;
            for (key in jsonData[i]) {
                rg = '{#' + key + '}';
                if (html1.indexOf(rg) != -1) {
                    html1 = html1.toString().ReplaceAll(rg, jsonData[i][key]);
                }
            }

            if (txt == null) {
                txt = html1;
            }
            else {
                txt += connectSign + html1;
            }
        }

        if (txt == null) {
            txt = HtmlOfDataSign;
        }
        return txt;
    }

    function selectedValueObject(obj, value1) {
        if (obj == null) return;
        var i = 0;
        var ii = -1;
        var v = '';
        $(obj).children('option').each(function () {
            v = $(this).attr('value');
            v = v == null ? '' : v;
            if (v == value1 && ii == -1) {
                $(this).attr('selected', true);
                ii = i;
            }
            i++;
        });
    }

    function selectedTextObject(obj, text1) {
        if (obj == null) return;
        var i = 0;
        var ii = -1;
        var v = '';
        $(obj).children('option').each(function () {
            v = $(this).text();
            v = v == null ? '' : v;
            if (v == text1 && ii == -1) {
                $(this).attr('selected', true);
                ii = i;
            }
            i++;
        });
    }
        
    function getJsonString(contrainerId1, tagName) {
        var ContrainerId0 = DataOperate.ContrainerId;
        if (contrainerId1 != null) {
            if (contrainerId1.toString().length > 0) {
                ContrainerId0 = contrainerId1;
            }
        }

        if (ContrainerId0 == null) return;
        if (ContrainerId0.toString().length == 0) return;

        _fieldSign = _fieldSign == null ? 'field' : _fieldSign;
        _fieldSign = _fieldSign.toString().length == 0 ? 'field' : _fieldSign;
        
        var myfield = "";
        var fvalue = '';
        var ctype = '';
        var Id = "";
        var labelObj = null;
        var ischecked = false;
        var mbool = true;
        var dt = null;

        $('#' + ContrainerId0).find('input').each(function () {
            myfield = $(this).attr(_fieldSign);
            myfield = myfield == null ? '' : myfield;
            myfield = myfield.toString().length == 0 ? null : myfield;
            if (myfield != null) {
                mbool = true;

                ctype = $(this).attr('type');
                if (ctype == 'text' || ctype == 'password') {
                    fvalue = $(this).attr('value');
                }
                else if (ctype == 'radio') {
                    mbool = false;
                    ischecked = $(this).attr('checked');
                    if (ischecked.toString().toLowerCase() == 'true' || ischecked.toString().toLowerCase() == 'checked') {
                        Id = $(this).attr('id');
                        labelObj = null;
                        labelObj = $("label[for='" + Id + "']");
                        if (labelObj != null) {
                            fvalue = $(labelObj).text();
                            mbool = true;
                        }
                    }
                }
                else if (ctype == 'checkbox') {
                    ischecked = $(this).attr('checked');
                    if (ischecked.toString().toLowerCase() == 'true' || ischecked.toString().toLowerCase() == 'checked') {
                        Id = $(this).attr('id');
                        labelObj = null;
                        labelObj = $("label[for='" + Id + "']");
                        if (labelObj != null) {
                            fvalue = $(labelObj).text();
                        }
                    }
                }

                if (mbool) {
                    if (dt == null) {
                        if (fvalue.toString().length == 0) {
                            dt = myfield + ':\'' + fvalue + '\'';
                        }
                        else {
                            if (isNaN(fvalue)) {
                                dt = myfield + ':\'' + fvalue + '\'';
                            }
                            else {
                                dt = myfield + ':' + fvalue;
                            }
                        }
                    }
                    else {
                        if (fvalue.toString().length == 0) {
                            dt += "," + myfield + ':\'' + fvalue + '\'';
                        }
                        else {
                            if (isNaN(fvalue)) {
                                dt += "," + myfield + ':\'' + fvalue + '\'';
                            }
                            else {
                                dt += "," + myfield + ':' + fvalue;
                            }
                        }
                    }
                } //mbool == true
                
            }

        });

        $('#' + ContrainerId0).find('div').each(function () {
            myfield = $(this).attr(_fieldSign);
            myfield = myfield == null ? '' : myfield;
            myfield = myfield.toString().length == 0 ? null : myfield;
            if (myfield != null) {
                fvalue = $(this).text();
                if (dt == null) {
                    if (fvalue.toString().length == 0) {
                        dt = myfield + ':\'' + fvalue + '\'';
                    }
                    else {
                        if (isNaN(fvalue)) {
                            dt = myfield + ':\'' + fvalue + '\'';
                        }
                        else {
                            dt = myfield + ':' + fvalue;
                        }
                    }
                }
                else {
                    if (fvalue.toString().length == 0) {
                        dt += "," + myfield + ':\'' + fvalue + '\'';
                    }
                    else {
                        if (isNaN(fvalue)) {
                            dt += "," + myfield + ':\'' + fvalue + '\'';
                        }
                        else {
                            dt += "," + myfield + ':' + fvalue;
                        }
                    }
                }
            }
        });

        if (dt != null) {
            dt = "{" + dt + "}";
        }

        return dt;
    }

    return this;
}

String.prototype.ReplaceAll = function (sign, target) {
    var str = this;
    if (str == null) return str;
    if (sign == null) return str;
    if (sign.toString().length == 0) return str;
    if (target == null) return str;
    if ((sign == target) || (str.toString().length == 0)) return str;
    while (str.toString().indexOf(sign) != -1) {
        str = str.replace(sign, target);
    }
    return str;
}

///获取cookie值NameOfCookie的名称
function Get_Cookie(NameOfCookie) {
    if (document.cookie.length > 0) {
        begin = document.cookie.indexOf(NameOfCookie + "=");
        if (begin != -1) {
            begin += NameOfCookie.length + 1; //cookie值的初始位置
            end = document.cookie.indexOf(";", begin); //结束位置
            if (end == -1) { end = document.cookie.length; }  //没有;则end为字符串结束位置
            return unescape(document.cookie.substring(begin, end));
        }
    }

    return null;
}

//arr值：2级数组，例：new Array(["ID","1"],["UserName","admin"],["Password","123"])
function Set_Cookie(arr, hour) {
    try {
        var s = arr[0][0];
    }
    catch (e) {
        //return;
    }

    hour = hour == null ? '' : hour;
    hour = hour.toString().length == 0 ? 24 : hour;
    hour = isNaN(hour) ? 24 : hour;

    var cookieStr = "";
    var n = arr.length;

    var myValue = "";
    var date = new Date();
    var expireDays = 1;
    date.setTime(date.getTime() + expireDays * 24 * 3600 * 1000);
    var expire = ";expire=" + date.toGMTString();
    for (var i = 0; i < n; i++) {
        myValue = arr[i][1];
        cookieStr = arr[i][0] + "=" + escape(myValue) + expire;
        window.document.cookie = cookieStr + ';path=/';
    }

}

function onKey_downOnlyNum(e) {
    var n = e.keyCode;
    if (n == 47 || (n < 46 && n > 8) || n < 8 || (n > 57 && n < 96) || (n > 105 && n < 110) || (n > 110 && n < 190) || n > 190) {
        return false;
    }
    return true;
}

function findParentObject(tag_Name, like) {
    var obj = null;
    var ID = "";
    var objArr = parent.window.document.getElementsByTagName(tag_Name);
    var len = objArr.length;
    for (var i = 0; i < len; i++) {
        ID = objArr[i].id + "";
        if (ID.indexOf(like) != -1) {
            obj = objArr[i];
            break;
        }
    }

    return obj;
}

function findObject(tag_Name, like, filter) {
    var obj = null;
    var ID = "";
    var objArr = window.document.getElementsByTagName(tag_Name);
    var len = objArr.length;
    var typeV = "";
    var nameV = "";

    for (var i = 0; i < len; i++) {
        try {
            typeV = objArr[i].getAttributeNode("type").value + "";
        } catch (e) {

        }

        ID = objArr[i].id + "";
        if (ID.indexOf(like) != -1) {
            if (filter == null) {
                obj = objArr[i];
                break;
            }
            else if (filter.length == 0) {
                obj = objArr[i];
                break;
            }
            else if (ID.indexOf(filter) == -1) {
                obj = objArr[i];
                break;
            }
        }

        if (typeV.toLowerCase() == "radio") {
            //            nameV = objArr[i].getAttributeNode("name").value + "";
            //            
            //            if (nameV.indexOf(like) != -1) {
            //                if (filter == null) {
            //                    obj = objArr[i];
            //                    break;
            //                }
            //                else if (filter.length == 0) {
            //                    obj = objArr[i];
            //                    break;
            //                }
            //                else if (nameV.indexOf(filter) == -1) {
            //                    obj = objArr[i];
            //                    break;
            //                }
            //            }
        }
        else {

        }

    }

    return obj;
}

function findObjectN(like) {
    var obj = null;
    var ID = "";
    var tagArr = new Array("form", "iframe", "textarea", "input", "select", "label", "span", "div", "table", "tr", "td");
    var objArr = null;
    var len = 0;
    var nlen = tagArr.length;
    var tagName = "";
    var typeV = "";
    var nameV = "";

    for (var n = 0; n < nlen; n++) {
        tagName = tagArr[n] + "";
        objArr = window.document.getElementsByTagName(tagName);
        len = objArr.length;
        for (var i = 0; i < len; i++) {
            try {
                typeV = objArr[i].getAttributNode("type").value + "";
            } catch (e) {

            }

            if (typeV.toLowerCase() == "radio") {
                nameV = objArr[i].getAttributeNode("name").value + "";
                if (nameV.indexOf(like) != -1) {
                    obj = objArr[i];
                    break;
                }
            }
            else {
                ID = objArr[i].id + "";
                if (ID.indexOf(like) != -1) {
                    obj = objArr[i];
                    break;
                }
            }
        }

        if (obj != null) {
            break;
        }
    }

    return obj;
}

function findObjectArray(tag_Name, like) {
    var objArr = new Array();
    var ID = "";
    var obj = window.document.getElementsByTagName(tag_Name);
    var len = obj.length;
    var n = 0;
    var typeV = "";
    var nameV = "";

    for (var i = 0; i < len; i++) {
        try {
            typeV = obj[i].getAttributNode("type").value + "";
        } catch (e) {

        }

        if (typeV.toLowerCase() == "radio") {
            nameV = obj[i].getAttributeNode("name").value + "";
            if (nameV.indexOf(like) != -1) {
                objArr[n] = obj[i];
                n++;
            }
        }
        else {
            ID = obj[i].id + "";
            if (ID.indexOf(like) != -1) {
                objArr[n] = obj[i];
                n++;
            }
        }
    }

    return objArr;
}

MessageBox = function (Width) {
    Width = Width == null ? 200 : Width;
        
    MessageBox.prototype.Show = function (title, msg) {
        Ext.onReady(function () {
            var msgBox = function (title, msg) {
                Ext.Msg.show({
                    title: title,
                    msg: msg,
                    minWidth: 200,
                    modal: true,
                    icon: Ext.Msg.INFO,
                    buttons: Ext.Msg.OK
                });
            };

            msgBox(title, msg);
        });
    }
    
    return this;
}

GetAttrValue = function (Json, Pars) {
    var JsonObject = eval("(" + Json + ")");
    var nlen = JsonObject.length;
    if (nlen == 0) return Pars;

    for (var objJ in JsonObject[0]) {
        for (var objP in Pars) {
            if (objJ.toString().toLowerCase() == objP.toString().toLowerCase()) {
                Pars[objP] = JsonObject[0][objJ];
            }
        }
    }

    return Pars;
}

RemoveSelectOption = function (obj) {
    while (obj.options.length > 0) {
        obj.options.remove(0);
    }
}

selectedValueObject = function (obj, value1) {
    var len = obj.options.length;
    var v = "";
    for (var i = 0; i < len; i++) {
        v = obj.options[i].value + "";
        if (v == value1) {
            obj.selectedIndex = i;
            break;
        }
    }
}

selectedTextObject = function (obj, text1) {
    var len = obj.options.length;
    var v = "";
    for (var i = 0; i < len; i++) {
        v = obj.options[i].text + "";
        if (v == text1) {
            obj.selectedIndex = i;
            break;
        }
    }
}

initialSelect = function (obj, textArr, valueArr, zeroText, zeroValue, defaultSelectedValue) {
    var len = textArr.length;
    var len1 = valueArr.length;

    RemoveSelectOption(obj);

    if (len != len1) return;

    if (textArr == null) return;
    if (valueArr == null) return;

    var objOption = window.document.createElement("option");
    objOption.text = zeroText;
    objOption.value = zeroValue;
    obj.add(objOption);

    for (var i = 0; i < len; i++) {
        objOption = window.document.createElement("option");
        objOption.text = textArr[i];
        objOption.value = valueArr[i];
        obj.add(objOption);
    }

    len = obj.options.length;
    for (var i = 0; i < len; i++) {
        if (obj.options[i].value == defaultSelectedValue) {
            obj.selectedIndex = i;
        }
    }
}

BigintToDatetime = function (bigint) {
    bigint = bigint + "";
    if (bigint.length != 14) return bigint;
    var year = bigint.substr(0, 4);
    var month = bigint.substr(4, 2);
    var day = bigint.substr(6, 2);
    var hour = bigint.substr(8, 2);
    var minuter = bigint.substr(10, 2);
    var second = bigint.substr(12, 2);

    return year + "-" + month + "-" + day + " " + hour + ":" + minuter + ":" + second;
}

ReplaceAll = function (str, source, target) {
    str = str + "";
    if (str.length == 0) return "";

    while (str.indexOf(source) != -1) {
        str = str.replace(source, target);
    }

    return str;
}

getOnlyDate = function (dt) {
    if (dt == null) return "";
    if (dt.length == 0) return dt;
    if (dt.indexOf(" ") == -1) return dt;
    var arr = dt.split(" ");
    return arr[0];
}

clearDotZero = function (num) {
    if (num == null) return "";
    if (num.indexOf(".") == -1) return num;
    if (isNaN(num)) return num;
    var arr = num.split(".");
    var s1 = arr[1] + "";
    var len = s1.length;
    var dotZero = arr[1] + "";
    var s = "";
    len--;
    for (var i = len; i >= 0; i--) {
        s = s1.substr(i, 1);
        if (s == "0") {
            dotZero = s1.substr(0, i);
        }
        else {
            break;
        }
    }

    if (dotZero == "") {
        dotZero = arr[0];
    }
    else {
        dotZero = arr[0] + "." + dotZero;
    }
    return dotZero;
}

ProhibitOpt = function (divID) {
    if (divID == null) return;

    var w = $(window).width();
    var h = $(window).height();

    $('#' + divID).css({ 'left': '0px', 'top': '0px', 'width': w + 'px', 'height': h + 'px', 'display': 'block' });
    $('#' + divID).fadeIn();
}

unProhibitOpt = function (divID) {
    if (divID == null) return;

    var w = 0;
    var h = 0;

    $('#' + divID).css({ 'left': '0px', 'top': '0px', 'width': w + 'px', 'height': h + 'px', 'display': 'block' });
    $('#' + divID).fadeOut();
}

function input_number() {
    $("input[type='text'][constraint='number']").each(function () {
        try {
            $(this).unbind('keyup');
        } catch (e) {

        }

        $(this).bind('keyup', function () {
            var m_b = onKey_downOnlyNum(event);
            var v1 = $(this).val();
            if (m_b == false) {
                if (v1.toString().length > 0) {
                    var pos = $(this).djGetCursorPos();
                    if (v1.toString().length > pos) {
                        var v2 = v1.substr(0, pos - 1);
                        var v3 = v1.substr(pos);
                        v1 = v2.toString() + "" + v3.toString();
                    }
                    else {
                        v1 = v1.substr(0, pos - 1);
                    }
                    
                    pos = pos == 0 ? 1 : pos;
                    $(this).val(v1);
                    $(this).djSetCursorPos(pos - 1);
                }
            }
        });
    });

    $("input[type='password'][constraint='number']").each(function () {
        try {
            $(this).unbind('keyup');
        } catch (e) {

        }

        $(this).bind('keyup', function () {
            var m_b = onKey_downOnlyNum(event);
            var v1 = $(this).val();
            if (m_b == false) {
                if (v1.toString().length > 0) {
                    var pos = $(this).djGetCursorPos();
                    if (v1.toString().length > pos) {
                        var v2 = v1.substr(0, pos - 1);
                        var v3 = v1.substr(pos);
                        v1 = v2.toString() + "" + v3.toString();
                    }
                    else {
                        v1 = v1.substr(0, pos - 1);
                    }

                    pos = pos == 0 ? 1 : pos;
                    $(this)[0].value = v1;
                    $(this).djSetCursorPos(pos - 1);
                }
            }
        });
    });
}
setTimeout(input_number, 500);



