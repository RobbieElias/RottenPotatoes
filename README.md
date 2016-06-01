# Rotten Potatoes
Movie Recommender System for University of Ottawa CSI2132 Group Project

**By:** Robbie Elias, Isaac Shannon, Jesse Desjardins

**Note:** To run the site, create a schema called 'MovieRecommender'. Then execute the 'raw.sql' and then 'seed.sql' scripts. Create a login role for the schema, and make sure you add a 'search_path' variable which is set to the value of 'MovieRecommender'. You can do so with the following command (assuming the user is csi2132): 'ALTER ROLE csi2132 SET search_path = "MovieRecommender";'.

Make sure to modify the 'Web/includes/settings.ini.php' to match your settings.
