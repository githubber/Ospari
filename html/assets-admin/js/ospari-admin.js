
Ospari = {
    doAutoSave: 0,
    initDraft: function() {
        Ospari.autoSaveURL = location.pathname.replace(/\/(create|edit\/(.*))/, '/auto-save');
        setInterval(Ospari.autoSave, 3000);

        $('#btn-save-draft').click(Ospari.saveAsDraft);
        $('#btn-publish').click(Ospari.publishDraft);

       


    },
    autoSave: function() {
        if (Ospari.doAutoSave === 0) {
            // console.log('auto save = 0');
            return;
        }

        Ospari.doAutoSave = 0;

        form = $('#draft-form');
        $('#draft-state-input').val(0);

        title = $('#draft-form').find("input[name=title]").val();
        content = $('#draft-content-textarea').val();

        if (!content && !title) {
            return;
        }


        callback = function(res) {
            if (res.success) {
                Ospari.doAutoSave = 0;
                $('#auto-save-msg').html(res.message);
                $('#draft-slug-bx').html(res.draft_slug);

                $('#draft-id-input').val(res.draft_id);
            } else {
                bootbox.alert(res.message);
            }
        };

        $.post(Ospari.autoSaveURL, $(form).serialize(), callback);

    },
    saveAsDraft: function() {
        $('#btn-save-draft i:first').remove();
        $('#btn-save-draft').append(' <i class="fa fa-refresh fa-spin"></i>');
        
        form = $('#draft-form');
        $('#draft-state-input').val(0);
        callback = function(res) {
            bootbox.hideAll();
            if (res.success) {
                //bootbox.alert(res.message);
                $('#btn-save-draft i:first').remove();
                $('#btn-save-draft').append(' <i class="fa fa-check"></i>');
            } else {
                bootbox.alert(res.message);
            }

        };
        $.post($(form).attr('action'), $(form).serialize(), callback);
        return false;
    },
    publishDraft: function() {
        $('#btn-publish i:first').remove();
        $('#btn-publish').append(' <i class="fa fa-refresh fa-spin"></i>');
        
        form = $('#draft-form');
        $('#draft-state-input').val(1);

        callback = function(res) {
            bootbox.hideAll();
            if (res.success) {
                $('#btn-publish i:first').remove();
                bootbox.dialog({
                    message: res.message,
                    //title: "Make Money Online with RankSider.com",
                    buttons: {
                        success: {
                            label: "View Post",
                            className: "btn btn-success",
                            callback: function() {
                                document.location = res.post_url;
                            }
                        },
                        main: {
                            label: "Close",
                            className: "btn bold",
                            callback: function() {

                            }
                        }
                    }
                })


            } else {
                bootbox.alert(res.message);
            }
        };
        $.post($(form).attr('action'), $(form).serialize(), callback);
        return false;
    }

};


