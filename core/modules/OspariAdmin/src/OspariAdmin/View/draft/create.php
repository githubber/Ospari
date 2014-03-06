<?php
$title = 'New Post';
$this->title = $title;

$form = $this->form;
$req = $this->req;

$this->setJS(OSPARI_URL . '/assets-admin/wysihtml5/wysihtml5-0.3.0.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/wysihtml5/bootstrap3-wysihtml5.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap3-typeahead.min.js');
$this->setJS(OSPARI_URL . '/assets-admin/marked/marked.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/jquery.autosize.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/dropzone.js');
$this->setJS(OSPARI_URL . '/assets-admin/js/bootstrap-tagsinput.min.js');

$this->setCSS(OSPARI_URL . '/assets-admin/wysihtml5/bootstrap-wysihtml5-0.0.2.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/dropzone.css');
$this->setCSS(OSPARI_URL . '/assets-admin/css/bootstrap-tagsinput.css');

//echo $form->toHTML_V3(O);
?>
<div class="col-lg-6">
    <form class="form-horizontal" id="draft-form" action="<?php echo $form->getAction(); ?>" method="post">
        <input type="hidden" id="draft-id-input" name="draft_id" value="<?php echo $req->getInt('draft_id') ?>">
        <input type="hidden" id="draft-state-input" name="state" value="<?php echo $req->getInt('draft_state') ?>">

        <?php
        echo $form->getElement('title')->toHTML_V3($mainCol = 'col-lg-0', $col_1 = 'col-lg-12');
        ?>

        <div class="form-group">
            <div class="col-lg-12">
                <?php
                //echo $form->getElement('content')->toHTML_V3($mainCol = 'col-lg-0', $col_1 = 'col-lg-12');
                echo $form->getElement('code')->addClass('form-control')->renderInput();
                ?>
            </div>

        </div>  
          <div class="form-group">
          <div class="col-lg-12">
          <?php
                    echo $form->getElement('tags')->addClass('form-control')->renderInput();
           ?>
          </div>
          </div>

        <div class="form-group">
            <div class="col-lg-4">
                

            </div>
            <div class="col-lg-4">

            </div>
            <div  class="col-lg-4">

                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-danger" id="btn-save-draft">Save as Draft</button>
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" id="btn-publish">Publish</a></li>

                    </ul>
                </div>

            </div>

        </div>
    </form>
</div>
<div class="col-lg-6">

    <div class="col-lg-12">
URL: <span id="draft-slug-bx" class="bold">
            <?php if ($req->slug) {
                echo $this->escape($req->slug);
                ?>
            <span> &NonBreakingSpace; <a href="#" title="Edit" onclick=" return Ospari.updateSlug();" id="edit-slug"><i class="fa fa-edit"></i></a></span>
            <?php
            } else {
                echo 'Would be generated from title.';
            }
            ?>
            </div>
                 </span>
    
<div class="col-lg-12 text-right">
    <span id="auto-save-msg" class="text-muted"></span>

     <span id="draft-preview-btn">
         <?php
            if( $req->id ){
                echo '<a href="'.OSPARI_URL.'/preview?draft_id='.$req->id.'" target="_preview"><i class="fa fa-external-link"></i> Preview</a>';
            }
         ?>
         
     </span>
</div>


    <div class="col-lg-12" id="editor-preview-title"></div>
    
    <div class="col-lg-12" id="editor-preview"></div>   

</div>



<script>
    OspariEditor = {
        initWYSIWYG: function() {
            $.fn.wysihtml5.defaultOptions['stylesheets'] = false;
            $.fn.wysihtml5.defaultOptions['font-styles'] = false;

            $('#draft-content-textarea').wysihtml5();

            $('#media-upload-btn').click(function() {
                bootbox.alert('<iframe src="' + $(this).attr('href') + '" class="upload-iframe"></iframe>');
                return false;
            });



            iframes = $('iframe.wysihtml5-sandbox');
            if (iframes.length) {
                iframes[0].contentDocument.onkeyup = function() {
                    var content = $('#draft-content-textarea').val();
                    $('#editor-preview').html(content);
                    Ospari.doAutoSave = 1;
                };
                iframes[0].contentDocument.onkeypress = function() {
                    content = $('#draft-content-textarea').val();
                    $('#editor-preview').html(content);
                };
            }

        },
        
        previewMarkdown: function(){
                content =  $('#draft-content-textarea').val();;
                content = OspariEditor.prepareDropzone(content);
                $('#editor-preview').html(marked(content));
                Ospari.doAutoSave = 1;
                OspariEditor.bindDropZone();
        },
        
        initMarkdown: function() {
            var content = $('#draft-content-textarea').val();
            $('#editor-preview').html(marked(content));
            $('#editor-preview-title').html('<h1>' + $('#nz-bt-title').val() + '</h1>');

            $('#draft-content-textarea').keydown(function() {
               OspariEditor.previewMarkdown();
            }).autosize();

            $('#draft-content-textarea').keyup(function() {
                 OspariEditor.previewMarkdown();
                
            });



            $('#nz-bt-title').keyup(function() {
                title = $(this).val();
                $('#editor-preview-title').html('<h1>' + title + '</h1>');
                Ospari.doAutoSave = 1;

            });
            $('#nz-bt-title').keydown(function() {
                title = $(this).val();
                $('#editor-preview-title').html('<h1>' + title + '</h1>');
                Ospari.doAutoSave = 1;

            });


        },
        prepareDropzone: function( content ){
            $('#dropzone').remove();
            var placeholder = '<div class="dropzone" id="dropzone"></div>';
            var text = content.replace('![]()', placeholder);
            return text;
        },
        
        bindDropZone: function(){
            var me = this;
            $("div#dropzone").dropzone(
                    { 
                        url: Ospari.adminURL+"/media/upload?draft_id="+$('#draft-id-input').val(),
                        parallelUploads:1,
                        maxFilesize:1,
                        paramName:'image',
                        uploadMultiple:false,
                        thumbnailWidth:400,
                        thumbnailHeight:300,
                        maxFiles:1,
                        addRemoveLinks:false,
                        init: function(){
                            this.on("error", function(file, message) {
                                this.removeFile(file);
                                bootbox.alert(message); 
                            });
                            
                            this.on("success", function(file, json, xmlHttp) { 
                                if(json.success){
                                   var content =  $('#draft-content-textarea').val();
                                    content  = content.replace('![]()','![alt text]('+json.message+' "Photo")');
                                     $('#draft-content-textarea').val(content);
                                      OspariEditor.previewMarkdown();
                                       Ospari.doAutoSave = 1;
                                      console.log('ospari is '+Ospari.doAutoSave);
                                      Ospari.autoSave();
                                }
                                else{
                                    this.removeFile(file);
                                    bootbox.alert(json.message);
                                }
                            });
                            
                            this.on("maxfilesexceeded", function(file) {
                                this.removeFile(file);
                            });
                        }
                    }
                 );
        },
        initTypeAhead: function() {
            $('#tag-input').typeahead({
                //remote: '/de/board_dev/keyword-search?q=%QUERY'
                source: function(query, process) {
                    return $.get('<?php echo OSPARI_URL; ?>/keyword-search', {q: query}, function(data) {
                        return process(data);
                    });
                }

            });
        }
    };
    $(document).ready(
            function() {
                OspariEditor.initMarkdown();
                Ospari.initDraft();
                Ospari.blogURL = '<?php echo OSPARI_URL ?>'; 
                Ospari.adminURL = '<?php echo OSPARI_URL.'/'.OSPARI_ADMIN_PATH ?>';
                $('#tag-input').tagsinput({
                    typeahead:{
                         source: function(query) {
                            return $.get('<?php echo OSPARI_URL.'/'.OSPARI_ADMIN_PATH ?>'+'/tags');
                          }
                    }
                });
                $('#tag-input').bind('itemAdded', function(event){
                    $.post(
                            '<?php echo OSPARI_URL.'/'.OSPARI_ADMIN_PATH ?>'+'/tag/add',
                            {tag:event.item,draft_id:$('#draft-id-input').val()}, 
                            function(json){
                                if(!json.success){
                                        bootbox.alert(json.message);
                                    }
                            },
                            'json'
                          );
                });
                $('#tag-input').bind('itemRemoved', function(event){
                     $.post(
                             '<?php echo OSPARI_URL.'/'.OSPARI_ADMIN_PATH ?>'+'/tag/delete',
                             {tag:event.item,draft_id:$('#draft-id-input').val()}, 
                             function( json ){
                                    if(!json.success){
                                        bootbox.alert(json.message);
                                    }
                             },
                            'json');
                });
                $('.bootstrap-tagsinput','form').addClass('col-lg-12');
                
            }
    );
</script>
