<?php

namespace zibo\library\cli;

/**
 * Command line argument parser
 */
class ArgumentParser {

    /**
     * Parse the arguments for the command line interface
     *
     * <p>This method will parse the arguments which can be passed in different ways: variables, flags and/or values.</p>
     * <ul>
     * <li>--named-boolean</li>
     * <li>--named-variable="your value"</li>
     * <li>-f</li>
     * <li>-afc</li>
     * <li>plain values</li>
     * </ul>
     *
     * <p>An example:<br />
     * <p>index.php agenda/event/15 --detail --comments=no --title="Agenda events" -afc nice</p>
     * <p>will result in</p>
     * <p>array(<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;0 =&gt; "agenda/event/15"<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'detail' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'comments' =&gt; false<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'title' =&gt; "Agenda events"<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'a' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'f' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;'c' =&gt; true<br />
     * &nbsp;&nbsp;&nbsp;&nbsp;1 =&gt; "nice"<br />
     * )
     * </p>
     *
     * @param array $arguments
     * @return array Parsed arguments
     */
    public static function parseArguments(array $arguments) {
        array_shift($arguments);

        $parsedArguments = array();
        foreach ($arguments as $argument) {
            if (substr($argument, 0, 2) == '--') {
                // variables: --key=value or --key
                $eqPos = strpos($argument, '=');
                if ($eqPos === false) {
                    $key = substr($argument, 2);
                    $parsedArguments[$key] = isset($parsedArguments[$key]) ? $parsedArguments[$key] : true;
                } else {
                    $key = substr($argument, 2, $eqPos - 2);
                    $parsedArguments[$key] = substr($argument, $eqPos + 1);
                }
            } elseif (substr($argument, 0, 1) == '-') {
                // flags: -n or -arf
                if (substr($argument, 2, 1) == '='){
                    $key = substr($argument, 1, 1);
                    $out[$key] = substr($argument, 3);
                } else {
                    $flags = str_split(substr($argument, 1));
                    foreach ($flags as $flag){
                        $out[$flag] = isset($out[$flag]) ? $out[$flag] : true;
                    }
                }
            } else {
                // values
                $parsedArguments[] = $argument;
            }
        }

        return $parsedArguments;
    }

}