CREATE TABLE Users(
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR (50) UNIQUE,
    Email VARCHAR (50) NOT NULL,
    Password VARCHAR (60) NOT NULL,
    Verified INT DEFAULT 0,
    ProfilePic VARCHAR (50) NULL,
    );
    
    -- bawbawbaw : passWord123&
    --- Iroh : iroH232*
INSERT INTO Users VALUES
(1, 'bawbawbaw', 'bawbawbaw@gmail.com', '$2y$10$FAhpER8u.lR3UyELGxrB2u.jlgrMzaHUTi/8qizJilCjESjM6BpbC', 1, NULL),
(2, 'Iroh', 'Iroh@gmail.com', '$2y$10$gJMe4PWhOAkRlYHejs0hausuEPdL38HbRSDarq4z6NdC25U9Vo.vq', 0, NULL);

CREATE TABLE Notes(
    NoteID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR (50) UNIQUE,
    Location VARCHAR (150) NOT NULL,
    UserID INT NOT NULL,
    ModifiedDate DATETIME NOT NULL,
    AttachedImg VARCHAR(50) NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
    );
    
INSERT INTO Notes VALUES
(1, 'demo', 'notes/bawbawbaw/demo.txt', 1, NOW(), NULL);

CREATE TABLE Label(
    LabelID INT PRIMARY KEY AUTO_INCREMENT,
    NoteID INT NOT NULL,
    UserID INT NOT NULL,
    Label VARCHAR (30) NULL,
    FOREIGN KEY (NoteID) REFERENCES Notes(NoteID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
    );
    
CREATE TABLE Preferences(
    UserID INT NOT NULL,
    FontSize INT DEFAULT 14,
    Mode VARCHAR (5) DEFAULT 'LIGHT',
    Layout VARCHAR (4) DEFAULT 'GRID',
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
    );
    
INSERT INTO Preferences VALUES
(1, 14, 'LIGHT', 'LIST'),
(2, 14, 'DARK', 'GRID');

CREATE TABLE LockedNotes(
    NoteID INT NOT NULL,
    Password VARCHAR(60) NOT NULL,
    FOREIGN KEY (NoteID) REFERENCES Notes(NoteID)
    );
    
CREATE TABLE SharedNotes(
    NoteID INT NOT NULL,
    OwnerID INT NOT NULL,
    Collaborator INT NOT NULL,
    Role VARCHAR (6) NOT NULL,
    PRIMARY KEY (NoteID, OwnerID, Collaborator),
    FOREIGN KEY (NoteID) REFERENCES Notes(NoteID),
    FOREIGN KEY (OwnerID) REFERENCES Users(UserID),
    FOREIGN KEY (Collaborator) REFERENCES Users(UserID)
    );

insert into notes(NoteID, Title, Location, UserID, ModifiedData, AttachedImg) VALUES
(2, 'SharedNote', 'notes/bawbawbaw/SharedNote.txt', 1, NOW(), NULL);

insert into sharednotes(NoteID, OwnerID, Collaborator, Role) VALUES
(2, 1, 2, 'VIEWER');