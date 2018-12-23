INSERT INTO `address` (address_id, line_1, line_2, postal_code_id, city_id, time_modified) VALUES
  (1, 'Nedre Bondeveg 3', null, 2, 1, current_timestamp),
  (2, 'Øvre Flatåsveg 71D', null, 2, 2, current_timestamp),
  (3, 'Gjøaveien 5', null, 3, 3, current_timestamp),
  (4, 'Høgskoleringen 1', null, 4, 3, current_timestamp);


INSERT INTO `user` VALUES
  -- passwords = qwerty
  (1, 'andsto', '$2y$10$bGpbfNzb68jPXcWmRywsI.SsG9fefCUdw0QX5aG7ok0e38av0y1Ry', 'Andre', 'Storhaug', 'andr3.storhaug@gmail.com', 1, 1, current_timestamp, current_timestamp),
  (2, 'miklan', '$2y$10$bGpbfNzb68jPXcWmRywsI.SsG9fefCUdw0QX5aG7ok0e38av0y1Ry', 'Mikael', 'Langlo', 'mikael.langlo@gmail.com', 4, 1, current_timestamp, current_timestamp),
  (3, 'johndoe', '$2y$10$bGpbfNzb68jPXcWmRywsI.SsG9fefCUdw0QX5aG7ok0e38av0y1Ry', 'John', 'Doe', 'john.doe@example.com', 3, 1, current_timestamp, current_timestamp);


INSERT INTO `phone` VALUES
  (1, 87654321, 1),
  (3, 12345678, 1);

INSERT INTO `role_assignment` VALUES
  (1, 1),
  (2, 4),
  (3, 1);



INSERT INTO `library_branch` VALUES
  (1, 4,'Aalesund bibliotek'),
  (2, 3,'Oslo bibliotek');

INSERT INTO `library_branch_staff_assignment` VALUES
  (2, 1);



INSERT INTO `publisher` VALUES
  (1, 'McGraw Hill Higher Education'),
  (2, 'Harper Collins');


INSERT INTO `book_details` VALUES
  (9780071317108, 'Discrete Mathematics and Its Applications', 1, 4, 7, 2012, 602, 429.00,
   current_timestamp),
  (9780072263367, 'Unix', 1, 4, 2, 2006, 873, 497.00, current_timestamp),
  (9780064404990, 'The Lion, the Witch and the Wardrobe', 2, 4, 2006, 1950, 208, 799.00, current_timestamp),
  (9780060884826, 'Prince Caspian: The Return to Narnia', 2, 4, 2005, 1951, 873, 302.00, current_timestamp),
  (9780064405027, 'The Voyage of the Dawn Treader', 2, 4,1994, 1952, 256, 497.00, current_timestamp),
  (9780064471091, 'The Silver Chair', 2, 4, 2002, 1953, 374, 692.99, current_timestamp),
  (9780064471060, 'The Horse and His Boy', 2, 4, 2002, 1954, 256, 642.30, current_timestamp),
  (9780064405058, 'The Magician\'s Nephew', 2, 4, 1994, 1955, 208, 748.00, current_timestamp),
  (9780064471084, 'The Last Battle', 2, 4, 2002, 1956, 240, 341.00, current_timestamp);


INSERT INTO `book`VALUES
  (1, 9780071317108, 1),
  (2, 9780072263367, 1),
  (3, 9780064404990, 1),
  (4, 9780064404990, 2),
  (5, 9780060884826, 1),
  (6, 9780060884826, 2),
  (7, 9780064405027, 2),
  (8, 9780064471091, 1),
  (9, 9780064471060, 2),
  (10, 9780064405058, 1),
  (11, 9780064405058, 2),
  (12, 9780064471084, 1);

INSERT INTO `book_series`VALUES
  (1, 'The Chronicles of Narnia', current_timestamp),
  (2, 'Harry Potter', current_timestamp);

INSERT INTO `book_series_assignment`VALUES
  (9780064404990, 1),
  (9780060884826, 1),
  (9780064405027, 1),
  (9780064471091, 1),
  (9780064471060, 1),
  (9780064405058, 1),
  (9780064471084, 1);



INSERT INTO `book_genre_assignment` VALUES
  (9780071317108, 16),
  (9780072263367, 32),

  (9780064404990, 4),
  (9780060884826, 4),
  (9780064405027, 4),
  (9780064471091, 4),
  (9780064471060, 4),
  (9780064405058, 4),
  (9780064471084, 4),

  (9780064404990, 6),
  (9780060884826, 6),
  (9780064405027, 6),
  (9780064471091, 6),
  (9780064471060, 6),
  (9780064405058, 6),
  (9780064471084, 6),

  (9780064404990, 31),
  (9780060884826, 31),
  (9780064405027, 31),
  (9780064471091, 31),
  (9780064471060, 31),
  (9780064405058, 31),
  (9780064471084, 31);



INSERT INTO `author` VALUES
  (1, 'Kenneth H.', 'Rosen'),
  (2, 'Douglas A.', 'Host'),
  (3, 'Rachel', 'Klee'),
  (4, 'Clive', 'Staples Lewis');

INSERT INTO `author_list` VALUES
  (9780071317108, 1),
  (9780072263367, 1),
  (9780072263367, 2),
  (9780072263367, 3),
  (9780064404990, 4),
  (9780060884826, 4),
  (9780064405027, 4),
  (9780064471091, 4),
  (9780064471060, 4),
  (9780064405058, 4),
  (9780064471084, 4);



INSERT INTO `book_loan` VALUES
  (1, 1, 1, current_timestamp, current_timestamp + INTERVAL 7 DAY, current_timestamp),
  (2, 2, 1, current_timestamp, current_timestamp + INTERVAL 7 DAY, current_timestamp),
  (3, 4, 3, current_timestamp, current_timestamp + INTERVAL 7 DAY, current_timestamp);

INSERT INTO `book_return` VALUE
  (1, 1, current_timestamp);

INSERT INTO `waiting_list` VALUES
  (1, 9780071317108, 1),
  (2, 9780072263367, 1);

INSERT INTO `waiting_list_line` VALUES
  (1, 1, current_timestamp),
  (2, 1, current_timestamp);