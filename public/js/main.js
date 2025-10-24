$(document).ready(function () {
  window._token = $('meta[name="csrf-token"]').attr('content')

  moment.updateLocale('en', {
    week: {dow: 1} // Monday is the first day of the week
  })

  // Initialize datetimepicker with error handling
  if (typeof $.fn.datetimepicker !== 'undefined') {
    $('.date').datetimepicker({
      format: 'YYYY-MM-DD',
      locale: 'en',
      icons: {
        up: 'fas fa-chevron-up',
        down: 'fas fa-chevron-down',
        previous: 'fas fa-chevron-left',
        next: 'fas fa-chevron-right'
      }
    })

    $('.datetime').datetimepicker({
      format: 'YYYY-MM-DD HH:mm:ss',
      locale: 'en',
      sideBySide: true,
      icons: {
        up: 'fas fa-chevron-up',
        down: 'fas fa-chevron-down',
        previous: 'fas fa-chevron-left',
        next: 'fas fa-chevron-right'
      }
    })
  } else {
    console.warn('DateTimePicker plugin not loaded. Date/datetime functionality will not be available.');
  }

  // Initialize timepicker with error handling
  if (typeof $.fn.datetimepicker !== 'undefined') {
    $('.timepicker').datetimepicker({
      format: 'HH:mm:ss',
      icons: {
        up: 'fas fa-chevron-up',
        down: 'fas fa-chevron-down',
        previous: 'fas fa-chevron-left',
        next: 'fas fa-chevron-right'
      }
    })

    $('.lesson-timepicker').each(function() {
      const $input = $(this)
      const hasPrefill = !!$input.val()
      $input.datetimepicker({
        format: 'h:mm A',
        stepping: 30,
        minDate: moment().startOf('day').add(7, 'hours'), // 7:00 AM
        maxDate: moment().startOf('day').add(21, 'hours'), // 9:00 PM
        useCurrent: false, // do not auto-set current time
        icons: {
          up: 'fas fa-chevron-up',
          down: 'fas fa-chevron-down',
          previous: 'fas fa-chevron-left',
          next: 'fas fa-chevron-right'
        }
      })
      // If value exists (e.g., from query string), let it display as-is. Otherwise, keep empty.
      if (!hasPrefill) {
        $input.val('')
      }
    })
  } else {
    console.warn('DateTimePicker plugin not loaded. Timepicker functionality will not be available.');
  }

  $('.select-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', 'selected')
    $select2.trigger('change')
  })
  $('.deselect-all').click(function () {
    let $select2 = $(this).parent().siblings('.select2')
    $select2.find('option').prop('selected', '')
    $select2.trigger('change')
  })

  $('.select2').select2()

  $('.treeview').each(function () {
    var shouldExpand = false
    $(this).find('li').each(function () {
      if ($(this).hasClass('active')) {
        shouldExpand = true
      }
    })
    if (shouldExpand) {
      $(this).addClass('active')
    }
  })
})
