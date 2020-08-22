<?php 
use Rating\RatingController;

    $router->post('/{ratingid}/delete', 'Rating\RatingController@removeRating');
    $router->post('/{ratingid}/edit', 'Rating\RatingController@editRating');
    $router->post('/create', 'Rating\RatingController@setRating');
    $router->get('/{ratingid}/', 'Rating\RatingController@getRating');
?>