INSERT INTO emotions (id, emotion) VALUES
  (1, "amused"),
  (2, "sad"),
  (3, "excited"),
  (4, "uplifted"),
  (5, "scared"),
  (6, "inspired"),
  (7, "joyful"),
  (8, "weird"),
  (9, "sentimental");

INSERT INTO movies (id, title, poster_path, runtime, release_date, imdb_id) VALUES
  (1, "Captain America: Civil War", "rDT86hJCxnoOs4ARjrCiRej7pOi.jpg", 146, "2016-04-27", "tt3498820"),
  (2, "Zootopia", "pKop1BcfgHzaN26EnhqLmjVP6LQ.jpg", 108, "2016-03-04", "tt2948356"),
  (3, "Star Wars: The Force Awakens", "weUSwMdQIa3NaXVzwUoIIcAi85d.jpg", 136, "2015-12-16", "tt2488496"),
  (4, "Mad Max: Fury Road", "sARK4uNacjUY4ACu0QjQPG0OUmW.jpg", 120, "2015-05-14", "tt1392190"),
  (5, "The Boy", "nY8jTN68Mhdqu06U9qtqIHr5o85.jpg", 97, "2016-01-22", "tt3882082"),
  (6, "The Notebook", "gMfstesBXKdsHToAUXVPHujUDfb.jpg", 123, "2004-06-25", "tt0332280"),
  (7, "Titanic", "f9iH7Javzxokvnkiz2yHD1dcmUy.jpg", 194, "1998-01-09", "tt0120338"),
  (8, "Raiders of the Lost Ark", "44sKJOGP3fTm4QXBcIuqu0RkdP7.jpg", 115, "1981-06-12", "tt0082971"),
  (9, "Rush", "cjEepHZOZAwmK6nAj5jis6HV75E.jpg", 123, "2013-10-02", "tt1979320"),
  (10, "The Human Centipede (The First Sequence)", "rhy5WMyLVmYQ9PfEM60pg25E3TL.jpg", 92, "2009-04-26", "tt1467304"),
  (11, "(500) Days of Summer", "5SjtNPD1bb182vzQccvEUpXHFjN.jpg", 95, "2009-07-17", "tt1022603"),
  (12, "Spectre", "jR9tBigjRLIwDdycTc8LQZQYAtl.jpg", 148, "2015-11-05", "tt2379713"),
  (13, "Silver Linings Playbook", "ilrZAV2klTB0FLxLb01bOp5pzD9.jpg", 122, "2012-12-25", "tt1045658"),
  (14, "The Hangover", "eshEkiG7NmU4ekA8CtpIdYiYufZ.jpg", 100, "2009-06-05", "tt1119646"),
  (15, "Ghostbusters", "3FS3oBdorgczgfCkFi2u8ZTFfpS.jpg", 107, "1984-06-07", "tt0087332"),
  (16, "Deadpool", "inVq3FRqcYIRl2la8iZikYYxFNR.jpg", 108, "2016-02-10", "tt1431045"),
  (17, "The Hunger Games", "iLJdwmzrHFjFwI5lvYAT1gcpRuA.jpg", 142, "2012-03-23", "tt1392170"),
  (18, "Frozen", "jIjdFXKUNtdf1bwqMrhearpyjMj.jpg", 102, "2013-11-27", "tt2294629"),
  (19, "The Dark Knight", "1hRoyzDtpgMU7Dz4JF22RANzQO7.jpg", 152, "2008-07-18", "tt0468569"),
  (20, "Before Sunrise", "9NBjDNPHA6SkThIweOs8iCfsA8a.jpg", 105, "1995-01-27", "tt0112471");

INSERT INTO reviews (id, movie_id, emotion_id, review_date) VALUES
  (1, 1, 3, "2016-07-04"),
  (2, 20, 9, "2004-05-22"),
  (3, 2, 1, "2016-03-12"),
  (4, 19, 3, "2009-08-19"),
  (5, 3, 3, "2015-12-25"),
  (6, 18, 1, "2014-01-30"),
  (7, 4, 3, "2015-12-15"),
  (8, 17, 3, "2013-12-13"),
  (9, 5, 5, "2016-04-07"),
  (10, 16, 7, "2016-02-14"),
  (11, 6, 2, "2012-07-23"),
  (12, 15, 5, "2014-05-28"),
  (13, 7, 2, "2005-06-16"),
  (14, 14, 7, "2009-06-20"),
  (15, 8, 3, "1982-06-12"),
  (16, 13, 4, "2012-12-30"),
  (17, 9, 6, "2013-10-17"),
  (18, 12, 3, "2015-12-14"),
  (19, 10, 8, "2009-06-21"),
  (20, 11, 4, "2009-07-28"),
  (21, 16, 3, "2016-03-30"),
  (22, 3, 6, "2016-02-20");
