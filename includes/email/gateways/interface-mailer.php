<?php

interface WeForms_Mailer_Contract {

    public function send( $to, $subject, $body, $headers );

}