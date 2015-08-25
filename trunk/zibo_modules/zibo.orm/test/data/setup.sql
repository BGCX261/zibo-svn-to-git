
INSERT INTO `Blog` (`id`, `title`, `text`) VALUES
(1, 'First blog', 'lorum ipsum'),
(2, 'Second blog', 'lorum ipsum'),
(3, 'Third blog', 'lorum ipsum');

INSERT INTO `BlogComment` (`id`, `blog`, `name`, `email`, `comment`) VALUES
(1, 1, 'John Doe', 'john.doe@gmail.com', 'First comment'),
(2, 3, 'Jane Doe', 'jane.doe@gmail.com', 'Second comment'),
(3, 3, 'John Doe', 'john.doe@gmail.com', 'Whooooooooooaaaa');

INSERT INTO `Node` (`id`, `name`, `parent`) VALUES
(1, 'root', NULL),
(2, 'branch1', 1),
(3, 'branch2', 1),
(4, 'leaf1', 2),
(5, 'leaf2', 2),
(6, 'leaf3', 3),
(7, 'leaf4', 1);

INSERT INTO `Permission` (`id`, `name`) VALUES
(1, 'permission1'),
(2, 'permission2');

INSERT INTO `PermissionRole` (`id`, `role`, `permission`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2);

INSERT INTO `Profile` (`id`, `user`, `extra`) VALUES
(1, 1, 'extra');

INSERT INTO `Role` (`id`, `name`) VALUES
(1, 'role1'),
(2, 'role2'),
(3, 'role3');

INSERT INTO `Single` (`id`, `name`, `description`) VALUES
(1, 'testName', 'testDescription');

INSERT INTO `User` (`id`, `username`, `password`) VALUES
(1, 'user1', 'secret'),
(2, 'user2', 's3cr3t');
