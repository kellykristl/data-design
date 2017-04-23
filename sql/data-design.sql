CREATE TABLE profile (
-- this creates the attribute for the primary key
-- auto_increment tells mySQL to number them {1, 2, 3, ...}
-- not null means the attribute is required!
profileId INT UNSIGNED AUTO_INCREMENT NOT NULL,
profileActivationToken CHAR(32),
profileAtHandle VARCHAR(32) NOT NULL,
-- to make sure duplicate data cannot exist, create a unique index
profileEmail VARCHAR(128) NOT NULL,
profileHash CHAR(128) NOT NULL,
-- to make something optional, exclude the not null
profilePhone VARCHAR(32),
profileSalt CHAR(64) NOT NULL,
UNIQUE(profileEmail),
UNIQUE(profileAtHandle),
-- this officiates the primary key for the entity
PRIMARY KEY(profileId)
);

CREATE TABLE product (
-- this is for yet another primary key...
productId INT UNSIGNED AUTO_INCREMENT NOT NULL,
-- this is for a foreign key; auto_incremented is omitted by design
productProfileId INT UNSIGNED NOT NULL,
productContent VARCHAR(140) NOT NULL,
productDate DATETIME(6) NOT NULL,
-- this creates an index before making a foreign key
INDEX(productProfileId),
-- this creates the actual foreign key relation
FOREIGN KEY(productProfileId) REFERENCES profile(profileId),
-- and finally create the primary key
PRIMARY KEY(productId)
);

CREATE TABLE favorite (
-- these are not auto_increment because they're still foreign keys
favoriteProfileId INT UNSIGNED NOT NULL,
favoriteProductId INT UNSIGNED NOT NULL,
favoriteDate DATETIME(6) NOT NULL,
-- index the foreign keys
INDEX(favoriteProfileId),
INDEX(favoriteProductId),
-- create the foreign key relations
FOREIGN KEY(favoriteProfileId) REFERENCES profile(profileId),
FOREIGN KEY(favoriteProductId) REFERENCES product(productId),
-- finally, create a composite foreign key with the two foreign keys
PRIMARY KEY(favoriteProfileId, favoriteProductId)
);