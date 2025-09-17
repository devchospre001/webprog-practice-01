var RestClient = {
  get: function (url, callback, error_callback) {
    $.ajax({
      url: Constants.get_api_base_url() + url,
      type: "GET",
      beforeSend: function (xhr) {
        if (Utils.get_from_localstorage("user")) {
          xhr.setRequestHeader(
            "Authentication",
            Utils.get_from_localstorage("user").token
          );
        }
      },
      success: function (response) {
        if (callback) callback(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (error_callback) error_callback(jqXHR);
      },
    });
  },
  request: function (url, method, data, callback, error_callback, headers) {
    var ajaxOptions = {
      url: Constants.get_api_base_url() + url,
      type: method,
      beforeSend: function (xhr) {
        if (Utils.get_from_localstorage("user")) {
          xhr.setRequestHeader(
            "Authentication",
            Utils.get_from_localstorage("user").token
          );
        }
        if (headers) {
          for (var key in headers) {
            xhr.setRequestHeader(key, headers[key]);
          }
        }
      },
      success: function (response) {
        if (callback) callback(response);
      },
      error: function (jqXHR) {
        if (error_callback) error_callback(jqXHR);
        else toastr.error(jqXHR.responseJSON?.message || "Request failed");
      }
    };
    // If JSON, set contentType and processData
    if (headers && headers["Content-Type"] === "application/json") {
      ajaxOptions.data = JSON.stringify(data);
      ajaxOptions.contentType = "application/json";
      ajaxOptions.processData = false;
    } else {
      ajaxOptions.data = data;
    }
    $.ajax(ajaxOptions);
  },
  post: function (url, data, callback, error_callback, headers) {
    RestClient.request(url, "POST", data, callback, error_callback, headers);
  },
  delete: function (url, data, callback, error_callback) {
    RestClient.request(url, "DELETE", data, callback, error_callback);
  },
  put: function (url, data, callback, error_callback) {
    RestClient.request(url, "PUT", data, callback, error_callback);
  },
};
