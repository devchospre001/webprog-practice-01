var FilmService = {
    delete_film: function(film_id) {
        if (
          confirm(
            "Do you want to delete film with the id " + film_id + "?"
          ) == true
        ) {
          console.log("TODO Perform deletion logic");
        }
    },
    edit_film: function(film_id){
        console.log("Get film with provided id, open modal and populate modal fields with data returned from the database");
        alert("Opened");
    }
}