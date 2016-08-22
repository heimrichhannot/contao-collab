var CollabBackend  = {

    /**
     * Toggle the task completion
     *
     * @param {object} el    The DOM element
     * @param {string} id    The ID of the target element
     * @param {string} table The table name
     *
     * @returns {boolean}
     */
    toggleTask: function (el, id, table) {

        el.blur();

        var item = $(el);

        if (item) {
            if (!el.value) {
                el.value = 1;
                el.checked = 'checked';
                new Request.Contao({field:el}).post({'action':'toggleTask', 'id':id, 'state':1, 'REQUEST_TOKEN':Contao.request_token});
            } else {
                el.value = '';
                el.checked = '';
                new Request.Contao({field:el}).post({'action':'toggleTask', 'id':id, 'state':0, 'REQUEST_TOKEN':Contao.request_token});
            }
            return;
        }
    }
};
