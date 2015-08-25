<?php

namespace zibo\library\mail\client;

use zibo\library\mail\MimePart;

/**
 * Common parser functions for the mail client
 */
class Parser {

    /**
     * Decodes a header value
     * @param string $value The value to decode
     * @return string Decoded value
     */
    public function decodeHeaderValue($value) {
        $result = '';

        $tokens = imap_mime_header_decode($value);
        foreach ($tokens as $token) {
            $result .= $token->text;
        }

        return $result;
    }

    /**
     * Decodes an encoded value
     * @param string $encoding The encoding used for the value
     * @param string $value The value to decode
     * @return string Decoded value
     */
    public function decodeEncodedValue($encoding, $value) {
        if ($encoding == 4 || $encoding == MimePart::ENCODING_QUOTED_PRINTABLE) {
            return quoted_printable_decode($value);
        }

        if ($encoding == 3 || $encoding == MimePart::ENCODING_BASE64) {
            return base64_decode($value);
        }

        return $value;
    }

    /**
     * Gets the filename from a message part
     * @param array $parameters Array with the parameters from a part of the imap_fetchstructure result
     * @return null|string
     */
    public function getPartFilename(array $parameters) {
        if (!$parameters) {
            return null;
        }

        foreach ($parameters as $parameter) {
            if ((strtoupper($parameter->attribute) == 'NAME') || (strtoupper($parameter->attribute) == 'FILENAME')) {
                return $parameter->value;
            }
        }

        return null;
    }

    /**
     * Gets the encoding of a message part
     * @param stdClass $part Result of imap_fetchstructure
     * @return string The encoding of the message part
     */
    public function getPartEncoding($part) {
        if ($part->encoding == 0) {
            return MimePart::ENCODING_7BIT;
        } elseif ($part->encoding == 1) {
            return MimePart::ENCODING_8BIT;
        } elseif ($part->encoding == 2) {
            return MimePart::ENCODING_BINARY;
        } elseif ($part->encoding == 3) {
            return MimePart::ENCODING_BASE64;
        } elseif ($part->encoding == 4) {
            return MimePart::ENCODING_QUOTED_PRINTABLE;
        }

        return null;
    }

    /**
     * Gets the MIME type of a message part
     * @param stdClass $part Result of imap_fetchstructure
     * @return string The MIME type of the message part
     */
    public function getPartMimeType($part) {
        $subtype = strtolower($part->subtype);
        if ($part->type == 7) {
            return 'application/' . $subtype;
        }

        $mime = '';
        if ($part->type == 0) {
            $mime .= 'text';
        } elseif ($part->type == 1) {
            $mime .= 'multipart';
        } elseif ($part->type == 2) {
            $mime .= 'message';
        } elseif ($part->type == 3) {
            $mime .= 'application';
        } elseif ($part->type == 4) {
            $mime .= 'audio';
        } elseif ($part->type == 5) {
            $mime .= 'image';
        } elseif ($part->type == 6) {
            $mime .= 'video';
        }

        return $mime . '/' . $subtype;
    }

}