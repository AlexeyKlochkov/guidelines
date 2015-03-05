/**
 * Created by aklochko on 2/23/15.
 */
CKEDITOR.plugins.add( 'anchor', {
    icons: 'anchor',
    requires:'dialog',
    init: function( editor ) {
        editor.addCommand( 'anchor', new CKEDITOR.dialogCommand( 'anchor',{

        } ) );
        editor.ui.addButton( 'anchor', {
            label: 'Insert anchor',
            command: 'anchor',
            toolbar: 'insert,10'
        });
    }
});