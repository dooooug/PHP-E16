<?php
 
// SETTINGS
$server     = '{imap.gmail.com:993/imap/ssl}INBOX';
$username   = 'heticallendar@gmail.com';
$password   = 'hetic2016';
$export_dir = 'export/'; #final slash is required
// END SETTINGS
 
$mbox = imap_open($server, $username, $password) or die('Unable to login');

// Getting all emails
if ($headers = imap_headers($mbox)) {
    $i = 0;
    foreach ($headers as $val) {
        $i ++;
 
        // Will return many infos about current email
        // Use var_dump($info) to check content
        $info   = imap_headerinfo($mbox, $i);
        $msgid  = trim($info->Msgno);
 
        // Gets the current email structure (including parts)
        // Use var_dump($structure) to check it out
        $structure = imap_fetchstructure($mbox, $msgid);
 
        // Getting attachments
        // Will return an array with all included files
        // Also works with inline attachments
        $attachments = get_attachments($structure);
 
        // You are now able to get attachments' raw content
        foreach ($attachments as $k => $at) {
            $filename = $export_dir.'id_'.$msgid.'_part_'.str_replace('.', '-', $at['part']).'_'.$at['filename'];
            echo $filename;
            $content = imap_fetchbody($mbox, $msgid, $at['part']);
 
            if ($content !== false && strlen($content) > 0 && $content != '') {
                switch ($at['encoding']) {
                    case '3':
                        $content = base64_decode($content);
                    break;
 
                    case '4':
                        $content = quoted_printable_decode($content);
                    break;
                }
 
                file_put_contents($filename, $content);
            }
        }
    }
}

imap_close($mbox);
echo "OK";

/**
* Gets all attachments
* Including inline images or such
* @author: Axel de Vignon
* @param $content: the email structure
* @param $part: not to be set, used for recursivity
* @return array(type, encoding, part, filename)
*
*/
function get_attachments($content, $part = null, $skip_parts = false) {
    static $results;
 
    // First round, emptying results
    if (is_null($part)) {
        $results = array();
    }
    else {
        // Removing first dot (.)
        if (substr($part, 0, 1) == '.') {
            $part = substr($part, 1);
        }
    }
 
    // Saving the current part
    $actualpart = $part;
    // Split on the "."
    $split = explode('.', $actualpart);
 
    // Skipping parts
    if (is_array($skip_parts)) {
        foreach ($skip_parts as $p) {
            // Removing a row off the array
            array_splice($split, $p, 1);
        }
        // Rebuilding part string
        $actualpart = implode('.', $split);
    }
 
    // Each time we get the RFC822 subtype, we skip
    // this part.
    if (strtolower($content->subtype) == 'rfc822') {
        // Never used before, initializing
        if (!is_array($skip_parts)) {
            $skip_parts = array();
        }
        // Adding this part into the skip list
        array_push($skip_parts, count($split));
    }
 
    // Checking ifdparameters
    if (isset($content->ifdparameters) && $content->ifdparameters == 1 && isset($content->dparameters) && is_array($content->dparameters)) {
        foreach ($content->dparameters as $object) {
            if (isset($object->attribute) && preg_match('~filename~i', $object->attribute)) {
                $results[] = array(
                'type'          => (isset($content->subtype)) ? $content->subtype : '',
                'encoding'      => $content->encoding,
                'part'          => empty($actualpart) ? 1 : $actualpart,
                'filename'      => $object->value
                );
            }
        }
    }
 
    // Checking ifparameters
    else if (isset($content->ifparameters) && $content->ifparameters == 1 && isset($content->parameters) && is_array($content->parameters)) {
        foreach ($content->parameters as $object) {
            if (isset($object->attribute) && preg_match('~name~i', $object->attribute)) {
                $results[] = array(
                'type'          => (isset($content->subtype)) ? $content->subtype : '',
                'encoding'      => $content->encoding,
                'part'          => empty($actualpart) ? 1 : $actualpart,
                'filename'      => $object->value
                );
            }
        }
    }
 
    // Recursivity
    if (isset($content->parts) && count($content->parts) > 0) {
        // Other parts into content
        foreach ($content->parts as $key => $parts) {
            get_attachments($parts, ($part.'.'.($key + 1)), $skip_parts);
        }
    }
    return $results;
}
