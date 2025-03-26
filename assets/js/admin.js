jQuery(window).on('elementor:init', function () {
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        panel.on('change', function (controlView) {
            var controlName = controlView.model.get('name');
            if (controlName === 'page_id' || controlName === 'post_id') {
                var selectedText = controlView.$el.find('select option:selected').text();

                // Obtenha o repeater control pelo model pai
                var repeaterItemModel = controlView.model.collection.parent;

                if (repeaterItemModel) {
                    if (controlName === 'page_id') {
                        repeaterItemModel.set('page_title', selectedText);
                    } else if (controlName === 'post_id') {
                        repeaterItemModel.set('post_title', selectedText);
                    }
                }
            }
        });
    });
});
