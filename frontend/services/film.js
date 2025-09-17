var FilmService = {
  load_categories: function () {
    RestClient.get("/film/performance", function (data) {
      var tbody = $("#film-performance tbody");
      tbody.empty();
      data.forEach(function (c) {
        tbody.append(`
                    <tr>
                        <td class="text-center">
                          <div class="btn-group" role="group">
                            <button class="btn btn-warning" onclick="FilmService.edit_film(${c.id})">Edit</button>
                            <button class="btn btn-danger" onclick="FilmService.delete_film(${c.id})">Delete</button>
                          </div>
                        </td>
                        <td>${c.id}</td>
                        <td>${c.name}</td>
                        <td>${c.total}</td>
                    </tr>
                `);
      });
    });
  },
  delete_film: function (film_id) {
    if (confirm("Do you want to delete film with id " + film_id + "?")) {
      RestClient.delete("/film/delete/" + film_id, {}, function (response) {
        toastr.success(response.message);
        FilmService.load_categories();
      }, function (jqXHR) {
        toastr.error(jqXHR.responseJSON.error || "Delete failed");
      });
    }
  },
  edit_film: function (film_id) {
    RestClient.get("/film/edit/" + film_id, function (film) {
      $("#edit-film-modal input[name='title']").val(film.title);
      $("#edit-film-modal input[name='description']").val(film.description);
      $("#edit-film-modal input[name='release_year']").val(film.release_year);
      $("#edit-film-modal").data("film_id", film_id).modal("show");
    });
  },
  save_edit: function () {
    var film_id = $("#edit-film-modal").data("film_id");
    var data = {
      title: $("#edit-film-modal input[name='title']").val(),
      description: $("#edit-film-modal input[name='description']").val(),
      release_year: $("#edit-film-modal input[name='release_year']").val()
    };
    RestClient.put("/film/edit/" + film_id, data, function (response) {
      toastr.success("Film updated successfully");
      $("#edit-film-modal").modal("hide");
      FilmService.load_categories();
    }, function (jqXHR) {
      toastr.error(jqXHR.responseJSON.error || "Update failed");
    });
  }
}