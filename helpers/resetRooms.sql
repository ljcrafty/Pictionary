DROP TABLE IF EXISTS Chat;
DROP TABLE IF EXISTS Rooms;

CREATE TABLE Rooms (
    username varchar(20) NOT NULL,
    roomCode varchar(5) NOT NULL,
    picDescr varchar(30),
    picture text,
    receivedUser varchar(20),
    picGuess varchar(30),
    score int DEFAULT 0,
    stage ENUM('start', 'drawing', 'drawn', 'guessing', 'guessed', 'forfeit') NOT NULL,
    token varchar(64),
    CONSTRAINT pk_Rooms PRIMARY KEY (username, roomCode), 
    CONSTRAINT fk_Rooms_user FOREIGN KEY (receivedUser) 
        REFERENCES Rooms(username) ON DELETE SET NULL
);

CREATE TABLE Chat (
    timeSent datetime NOT NULL,
    username varchar(20) NOT NULL,
    roomCode varchar(5) NOT NULL,
    chatMsg text NOT NULL,
    CONSTRAINT pk_Chat PRIMARY KEY (timeSent, username, roomCode)
);