init = ->
  jQuery ($) ->

    $('.restfulwidgets-parameter-type-draggable').draggable
      helper: ->
        $(this).clone false
      connectToSortable: '.restfulwidgets-request-widget-parameters-list'

    $('.restfulwidgets-parameter-type-draggable').each ->
      $(this).draggable
        containment: $(this).closest '.widget-inside'

    removeItem = false
    $('.restfulwidgets-request-widget-parameters-list').sortable
      items: '.restfulwidgets-parameter-type-sortable'
      placeholder: 'restfulwidgets-parameter-placeholder'
      over: ->
        removeItem = false
        return true
      beforeStop: (event, ui) ->
        ui.item.removeClass 'restfulwidgets-parameter-type-draggable'
                  .addClass 'restfulwidgets-parameter-type-sortable'
        ui.item.find '.restfulwidgets-parameter-options'
                  .removeClass 'hidden'
        ui.item.attr 'style', ''
        if removeItem
          ui.item.remove()
          removeItem = false
        return true

    $('.restfulwidgets-request-widget-parameters-list').droppable
      activate: ->
        $(this).addClass 'widget-hover'
      deactivate: ->
        $(this).removeClass 'widget-hover'

    $('.restfulwidgets-request-widget-parameters-list').each ->
      $(this).sortable
        containment: $(this).closest '.widget-inside'


    $('.restfulwidgets-request-parameter-types-list').droppable
      accept: '.restfulwidgets-parameter-type-sortable'
      drop: ->
        removeItem = true

    uriInput = $('.restfulwidgets-resource-location-input')
    uriInput.unbind 'focus.clearNotifications'
    uriInput.bind 'focus.clearNotifications', ->
      errorNotifcations = $(this)
                            .closest 'form'
                            .find '.restfulwidgets-input-error-notification'
      errorNotifcations.fadeOut()

    submitButtons = $('.restfulwidgets-request-widget-parameters-list')
                      .closest '.widget-inside'
                      .find 'input:submit'
    submitButtons.unbind 'click.restfulwidgetsCheckNames'
    submitButtons.bind 'click.restfulwidgetsCheckNames', (event) ->
      allNamed = true
      names = $(this)
                .closest '.widget-inside'
                .find '.restfulwidgets-request-widget-parameters-list'
                .find '.restfulwidgets-parameter-type-sortable'
                .find '.restfulwidgets-parameter-name'
                .map ->
                  $(this).val()
                .toArray()
      if (new Set names).size != names.length || names.indexOf('') > -1
        event.stopPropagation()
        event.preventDefault()
        errorNotifcation = $(this)
                            .closest '.widget-inside'
                            .find '.restfulwidgets-naming-error'
        errorNotifcation.fadeIn()

    submitButtons.unbind 'click.serializeFormData'
    submitButtons.bind 'click.serializeFormData', ->
      formData = {}
      sortable = $(this)
                  .closest '.widget-inside'
                  .find '.restfulwidgets-request-widget-parameters-list'
      sortable
        .find '.restfulwidgets-parameter-type-sortable'
        .each ->
          type = $(this)
                  .find '.restfulwidgets-parameter-type'
                  .val()
          name = $(this)
                  .find '.restfulwidgets-parameter-name'
                  .val()
          displayName = $(this)
                          .find '.restfulwidgets-parameter-display-name'
                          .val()
          formData[name] =
            type: type
            displayName: displayName
      serializedFormData = JSON.stringify formData
      $(this)
        .closest '.widget-inside'
        .find '.restfulwidgets-request-parameters-array'
        .val serializedFormData

jQuery document
  .ready init

jQuery document
  .ajaxStop init