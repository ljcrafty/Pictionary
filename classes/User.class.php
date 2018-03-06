<?php
    class User
    {
        private $data;
        private $keys = ['roomCode', 'username', 'stage', 'imgPath', 'imgDesc', 'received', 'guess', 'score'],
            $stages = ['start', 'drawn', 'guessed', 'forfeit'];

        public function __construct( $roomCode, $username, $stage, $imgPath = "", $imgDesc = "", 
            $received = "", $guess = "", $score = 0)
        {
            $this -> data['roomCode'] = $roomCode;
            $this -> data['username'] = $username;
            $this -> data['stage'] = $stage;
            $this -> data['imgPath'] = $imgPath;
            $this -> data['imgDesc'] = $imgDesc;
            $this -> data['received'] = $received;
            $this -> data['guess'] = $guess;
            $this -> data['score'] = $score;
        }

        /**
         * Getter for every piece of data in the object
         * key  -   the piece of data to get; possible values: roomCode, username, stage, 
         *              imgPath, imgDesc, received, guess, score
         */
        public function __get( $key )
        {
            if( array_key_exists($key, $data) )
            {
                return $data[$key];
            }
        }

        /**
         * Setter for every piece of data in the object
         * key  -   the piece of data to set; possible values: roomCode, username, stage, 
         *              imgPath, imgDesc, received, guess, score
         * val  -   the new value to give the object
         */
        public function __set( $key, $val )
        {
            if( array_key_exists($key, $keys) )
            {
                $data[$key] = $val;
            }
        }

        
    }
?>