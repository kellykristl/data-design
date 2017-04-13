CREATE TABLE profile (
-- this creates the attribute for the primary key
-- auto_increment tells mySQL to number them {1, 2, 3, ...}
-- not null means the attribute is required!
profileId INT UNSIGNED AUTO_INCREMENT NOT NULL,
profileActivationToken CHAR(32),
profileAtHandle VARCHAR(32) NOT NULL,
-- to make sure duplicate data cannot exist, create a unique index
profileEmail VARCHAR(128) UNIQUE NOT NULL,
profileHash CHAR(128) NOT NULL,
-- to make something optional, exclude the not null
profilePhone VARCHAR(32),
profileSalt CHAR(64) NOT NULL,
UNIQUE(profileEmail),
UNIQUE(profileAtHandle),
-- this officiates the primary key for the entity
PRIMARY KEY(profileId)
);

CREATE TABLE tweet (
-- this is for yet another primary key...
tweetId INT UNSIGNED AUTO_INCREMENT NOT NULL,
-- this is for a foreign key; auto_incremented is omitted by design
tweetProfileId INT UNSIGNED NOT NULL,
tweetContent VARCHAR(140) NOT NULL,
-- notice dates don't need a size parameter
tweetDate DATETIME NOT NULL,
-- this creates an index before making a foreign key
INDEX(tweetProfileId),
-- this creates the actual foreign key relation
FOREIGN KEY(tweetProfileId) REFERENCES profile(profileId),
-- and finally create the primary key
PRIMARY KEY(tweetId)
);

CREATE TABLE 'like' (
-- these are not auto_increment because they're still foreign keys
likeProfileId INT UNSIGNED NOT NULL,
likeTweetId INT UNSIGNED NOT NULL,
likeDate DATETIME NOT NULL,
-- index the foreign keys
INDEX(likeProfiileId),
INDEX(likeTweetId),
-- create the foreign key relations
FOREIGN KEY(likeProfileId) REFERENCES profile(profileId),
FOREIGN KEY(likeTweetId) REFERENCES tweet(tweetId),
-- finally, create a composite foreign key with the two foreign keys
PRIMARY KEY(likeProfileId, likeTweetId)
);