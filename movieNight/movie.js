/**
 * Created by ed on 8/11/17.
 */

var Movies = {}
Movies.show = function (error) {
    $("#listings").empty();
    if (error) {
        //render error mesage
    } else {
      //render listing for each movie returned
        if(Movies.listing && Movies.listing.length > 0) {
        for(var i=0; i<Movies.listing.length ; i++) {
            var movie = Movies.listing[i];
            var movielisting = this.listMovie(movie,i);
            $("#listings").append(movielisting);

            //add listener
            $("#movieLink_"+i).click(function(event){
                var movieID = event.currentTarget.id;
                var movieID = movieID.substring(movieID.indexOf('movieLink_')+10);
                Movies.showMovie(Movies.listing[movieID]);
                event.preventDefault();
            })
        }
        }else if(!Movies.listing || Movies.listing.length === 0){
            $("#listings").text('No Movies Found matching '+$("#search").val());

        }
}};

Movies.listMovie = function(m,postfix) {
    return '<div id="listTemplate" class="col-lg-6 movie-listing">' +
        '<h4><span id="title"> ' + m.Name + ' </span></h4>' +
        '<p><span id="Genre"> ' + m.Genre + ' </span> - <span id="minutes"></span> ' + m.RunTime + ' </p>' +
        '<p><span id="quick"> ' + m.Description + ' </span></p>' +
        '<p><a id="movieLink_'+postfix+'" class="btn-info btn-lg" href="#" role="button" >More...</a></p>' +
        '</div>' +
        '</div>';
};


Movies.showMovie = function(m) {
    var tmpl = ' '  +
        '<span class="mi-parts"><img src="'+ m.Image   + '" class="thumb" > </span> <br>'+
        '<span class="mi-parts">' +
        'Name: '    + m.Name    +                   '<br>'+
        'Genre: '   + m.Genre   +                   '<br>'+
        'Rating: '  + m.Rating  +        ' out of 10 <br>'+
        'Media: '   + m.Media   +                   '<br>'+
        'RunTime: ' + m.RunTime +          ' minutes <br>'+
        'IMDB: <a href="http://www.imdb.com/title/'    + m.IMDB    + '" target="_blank"> go to IMDB page</a><br>'+
        '</span>';
    $("#MovieInfo").empty().append(tmpl);
    $("#popup").fadeIn(400);
};

Movies.closeMovie = function() {
    $("#popup").fadeOut(400);
}

function readySet() {
    //add listener to input widget

    $("#searchButt").click(
        function () {
            var m = $("#search").val();
            var url = window.location.origin+window.location.pathname+ "api/search/"+encodeURIComponent(m);
            $.getJSON(url).done(function(result) {
                Movies.listing  = result;
                Movies.show();
            }).fail(function(err) {
                Movies.show(err);
                throw err;
            });
        }
    );

    $("#search").on('input',
        function () {
            var m = $("#search").val();
            var url = window.location.origin+window.location.pathname+ "api/search/"+encodeURIComponent(m);
console.log(url);
            $.getJSON(url).done(function(result) {
                console.log(result);
                Movies.listing  = result;
                Movies.show();
            }).fail(function(err) {

                console.log(err);
                Movies.show(err);
                throw err;
            });
        }
    );
    //add listener for closing of popup
    $("#popup").click(
        function () {
            Movies.closeMovie();
        }
    );

}

$( document).ready(function () {
    readySet();
});
