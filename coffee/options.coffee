init = ->
  jQuery ($) ->

    deleteButtons = $('.restfulwidgets_delete_row')
    deleteButtons.unbind 'click.restfulwidgetsDeleteRow'
    deleteButtons.bind 'click.restfulwidgetsDeleteRow', (event) ->
      rowName = $(event.target)
                  .closest 'td'
                  .find '.restfulwidgets_name_input'
                  .attr 'rowName'
      $.ajax
        url: ajaxurl
        type: 'POST'
        data:
          _restfulwidgets_ajax_api_table_nonce: $('#_restfulwidgets_ajax_api_table_nonce').val()
          action: '_restfulwidgets_ajax_update_api_table'
          deletedRow: rowName
        success: (response) ->
          response = $.parseJSON response
          if response.length
            $('#the-list')
              .html response
            init()

    formSubmit = $('#restfulwidgets-settings-form input:submit')
    formSubmit.unbind 'click.updateRowNames'
    formSubmit.bind 'click.updateRowNames', (event) ->
      urlInputs = $('.restfulwidgets_url_input')
      urlInputs.each ->
        rowName = $(this)
                    .closest 'tr'
                    .find '.restfulwidgets_name_input'
                    .val()
        if not rowName
          rowName = ''
        currentName = $(this)
                      .attr 'name'
        newName = currentName
                    .replace /\[[a-z0-9]*\]$/g, '[' + rowName + ']'
        $(this)
          .attr 'name', newName
      nameInputs = $('.restfulwidgets_name_input')
      if nameInputs.length > 1
        names = nameInputs.map -> $(this).val()
        .toArray()
        unique = true
        lowerCaseNames = names.map (value) -> value.toLowerCase()
        if (new Set lowerCaseNames).size != lowerCaseNames.length
          $('.restfulwidgets-input-error-notification')
            .fadeIn()
          event.preventDefault()

jQuery document
  .ready init

jQuery document
  .ajaxStop init