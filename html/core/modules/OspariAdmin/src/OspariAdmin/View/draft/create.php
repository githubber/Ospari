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


$this->setCSS(OSPARI_URL . '/assets-admin/wysihtml5/bootstrap-wysihtml5-0.0.2.css');

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
        <?php
        /**
          <div class="form-group">
          <div class="col-lg-11">
          <?php echo $form->getElement('cover')->addClass('form-control')->renderInput(); ?>

          </div>
          <div class="col-lg-1">
          <a href="<?php echo $this->uploadURL; ?>" traget="upload" id="media-upload-btn" class="btn btn-default btn-sx">Upload</a>
          </div>
          </div>
         * 
         */
        ?>
        <div class="form-group">
            <div class="col-lg-4">
                <?php
//echo $form->getElement('tags')->addClass('form-control')->renderInput();
                ?>

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
                    content = $('#draft-content-textarea').val();
                    $('#editor-preview').html(content)
                    Ospari.doAutoSave = 1;
                };
                iframes[0].contentDocument.onkeypress = function() {
                    content = $('#draft-content-textarea').val();
                    $('#editor-preview').html(content);
                };
            }

        },
        initMarkdown: function() {
            var content = $('#draft-content-textarea').val();
            $('#editor-preview').html(marked(content));
            $('#editor-preview-title').html('<h1>' + $('#nz-bt-title').val() + '</h1>');


            $('#draft-content-textarea').keydown(function() {
                content = $(this).val();
                $('#editor-preview').html(marked(content));
                Ospari.doAutoSave = 1;
            }).autosize();

            $('#draft-content-textarea').keyup(function() {
                content = $(this).val();
                $('#editor-preview').html(marked(content));
                Ospari.doAutoSave = 1;
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
    }
    $(document).ready(
            function() {
                OspariEditor.initMarkdown();
                Ospari.initDraft();
                Ospari.blogURL = '<?php echo OSPARI_URL ?>'; 
                spari.adminURL = '<?php echo OSPARI_URL.'/'.OSPARI_ADMIN_PATH ?>'; 
            }
    );
</script>
