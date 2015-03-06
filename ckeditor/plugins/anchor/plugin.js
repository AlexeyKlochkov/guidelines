/**
 * Created by aklochko on 2/23/15.
 */
CKEDITOR.plugins.add( 'anchor', {
    icons: 'anchor',
    requires:'dialog',
    init: function( editor ) {
        var types;
        var curType=new Array;
        $.ajax({
            url: 'handler.php',
            global: false,
            type: "POST",
            data: {request: "Get Section Types"},
            dataType: "json",
            success: function (result) {
                types= $.map(result['sectiontypes'], function(val,id) {
                    var tmp=[];
                    var tmp1=[];
                    tmp.push(val);
                    tmp.push(id);
                    tmp1.push(tmp);
                    return tmp1; });
                }});
        $.ajax({
            url: 'handler.php',
            global: false,
            type: "POST",
            data: {request: "Get Current Section Type",
                   sectionId:editor.name},
            dataType: "json",
            success: function (result) {
                if (result['curSectionKeywordId']!=undefined) {
                 curType[editor.name]=result['curSectionKeywordId'];
                }
            }});

        editor.ui.addButton( 'anchor', {
            label: 'Add keywords',
            command: 'anchor',
            toolbar: 'insert'
        });
        CKEDITOR.dialog.add( 'anchor', function( editor ) {
            return {
                title: 'Properties',
                minWidth: 400,
                minHeight: 200,
                contents: [
                    {
                        id: 'tab-basic',
                        label: 'Basic',
                        elements: [
                            {
                                type: 'select',
                                id: 'type',
                                label: 'Type of keyword',
                                items:types,
                                default:1,
                                validate: CKEDITOR.dialog.validate.notEmpty( "Type field cannot be empty." )
                            },
                            {
                                type: 'text',
                                id: 'name',
                                label: 'Value',
                                validate: CKEDITOR.dialog.validate.notEmpty( "Value field cannot be empty." )
                            }
                        ]
                    },
                    {
                        id: 'tab-adv',
                        label: 'Advanced',
                        elements: [
                            {
                                type: 'text',
                                id: 'id',
                                label: 'Id'
                            }
                        ]
                    }
                ],
                onShow : function()
                {
                    var curType=new Array;
                    $.ajax({
                        url: 'handler.php',
                        global: false,
                        type: "POST",
                        data: {request: "Get Current Section Type",
                            sectionId:editor.name},
                        dataType: "json",
                        success: function (result) {
                            if (result['curSectionKeywordId']!=undefined && result!='') {
                                curType[editor.name]=result['curSectionKeywordName'];
                                console.log(curType);
                                CKEDITOR.dialog.getCurrent().getContentElement('tab-basic','type').setValue(result['curSectionKeywordId']);
                                CKEDITOR.dialog.getCurrent().setValueOf('tab-basic','name',curType[editor.name]);
                            }
                        }});
                },
                onOk: function() {
                    var dialog = this;
                    var type=dialog.getValueOf('tab-basic','type');
                    var value=dialog.getValueOf('tab-basic','name');
                    $.ajax({
                        url: 'handler.php',
                        global: false,
                        type: "POST",
                        data: {request: "Save Anchor",
                            id:editor.name,
                            sectionId:type,
                            anchorValue:value
                            },
                        dataType: "json"
                        });
                }
            };
        });
    }
});