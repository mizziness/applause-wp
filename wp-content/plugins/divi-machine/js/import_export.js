jQuery(document).ready(function () {
  jQuery("button[data-success-callback='import_settings_machine']").attr("disabled", true);
  jQuery("#divi-machine_setting_file_upload").change(function () {
    jQuery("button[data-success-callback='import_settings_machine']").attr("disabled", false);
  });

});

function import_settings_data_filter_callback_machine(data) {
  data.file_id = jQuery("#setting_file_upload").val();
  return data;
}
function import_settings_machine() {
  location.reload();
}
function export_settings_machine(data) {
  var processRow = function (row) {
    var finalVal = '';
    for (var j = 0; j < row.length; j++) {
      var innerValue = row[j] === null ? '' : row[j].toString();
      if (row[j] instanceof Date) {
        innerValue = row[j].toLocaleString();
      }
      ;
      var result = innerValue.replace(/"/g, '""');
      if (result.search(/("|,|\n)/g) >= 0)
        result = '"' + result + '"';
      if (j > 0)
        finalVal += ',';
      finalVal += result;
    }
    return finalVal + '\n';
  };


  var csvFile = '';
  for (var i = 0; i < data.length; i++) {
    csvFile += processRow(data[i]);
  }

  downloadFile_machine(csvFile, 'divi-machine.csv')
}

function downloadFile_machine(data, fileName, type = "text/csv;charset=utf-8;") {
  const a = document.createElement("a");
  a.style.display = "none";
  document.body.appendChild(a);
  a.href = window.URL.createObjectURL(
    new Blob([data], { type })
  );
  a.setAttribute("download", fileName);
  a.click();
  window.URL.revokeObjectURL(a.href);
  document.body.removeChild(a);
}
