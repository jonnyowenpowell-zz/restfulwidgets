init = ->
  jQuery ($) ->
    
    fieldForms = $('.restfulwidgets-widget-request-form')
    fieldForms.unbind 'submit.restfulwidgetsRequestFormSubmit'
    fieldForms.bind 'submit.restfulwidgetsRequestFormSubmit', (event) ->
      baseUrl = $(this)
                  .find '.restfulwidgets-request-base-url'
                  .val()
      location = $(this)
                  .find '.restfulwidgets-request-string'
                  .val()
      data = {}
      $(this)
        .find '.restfulwidgets-parameter-input'
        .each ->
          if $(this).hasClass 'restfulwidgets-boolean-input'
            data[encodeURIComponent $(this).attr 'name'] = encodeURIComponent $(this).prop 'checked'
          else
            data[encodeURIComponent $(this).attr 'name'] = encodeURIComponent $(this).val()

      $.ajax
        url: encodeURI baseUrl + '/' + location
        type: 'GET'
        data: data
        context: $(this).parent()
        success: (response, textStatus, jqXHR) ->
          $(this)
            .find '.restfulwidgets-response-wrapper .response'
            .remove()
          contentType = jqXHR.getResponseHeader 'Content-Type'
          switch
            when /^image\/png$/g.test contentType
              $(this)
                .find '.restfulwidgets-response-wrapper'
                .append '<img class="response" src="data:image/png;base64,'+response+'">'
            when /^image\/jpeg$/g.test contentType
              $(this)
                .find '.restfulwidgets-response-wrapper'
                .append '<img class="response" src="data:image/jpeg;base64,'+response+'">'
            when /^text\/.*$/g.test contentType
              $(this)
                .find '.restfulwidgets-response-wrapper'
                .append '<p class="response">' + response + '</p>'
            else
              $(this)
                .find '.restfulwidgets-response-wrapper'
                .append '<p class="response">Unsupported Content-Type, displaying as text:' + response + '</p>'
          $(this)
            .find '.restfulwidgets-response-wrapper'
            .fadeIn()
        error: (jqXHR, textStatus, errorThrown) ->
          $(this)
            .find '.restfulwidgets-response-wrapper .response'
            .remove()
          $(this)
            .find '.restfulwidgets-response-wrapper'
            .append '<p class="response"> ERROR: ' + textStatus + '. Reason: ' + errorThrown + '.</p>'
            .fadeIn()

      event.preventDefault()

    integerInputs = $('.restfulwidgets-integer-input')
    integerInputs.unbind 'keypress.restfulwidgetsIntegerFilter'
    integerInputs.bind 'keypress.restfulwidgetsIntegerFilter', (event) ->
      if !/\d/.test(String.fromCharCode(event.keyCode))
        event.preventDefault()
      
    floatInputs = $('.restfulwidgets-float-input')
    floatInputs.unbind 'keypress.restfulwidgetsFloatFilter'
    floatInputs.bind 'keypress.restfulwidgetsFloatFilter', (event) ->
      if !/\d|\./.test(String.fromCharCode(event.keyCode))
        event.preventDefault()

    $('.restfulwidgets-slider').slider
      animate: true,
      min: 10,
      max: 30,
      step: 1,
      slide: (event, ui) ->
        $('.restfulwidgets-slider-value')
          .find 'span'
          .text ui.value
        $('.restfulwidgets-slider-value')
          .find 'input'
          .attr 'value', ui.value

jQuery document
  .ready init

jQuery document
  .ajaxStop init