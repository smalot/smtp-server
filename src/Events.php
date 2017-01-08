<?php

namespace SamIT\React\Smtp;

/**
 * Class Events
 * @package SamIT\React\Smtp
 */
final class Events
{
    const CONNECTION_CHANGE_STATE = 'smtp_server.connection.change_state';

    const CONNECTION_HELO_RECEIVED = 'smtp_server.connection.helo_received';

    const CONNECTION_FROM_RECEIVED = 'smtp_server.connection.from_received';

    const CONNECTION_RCPT_RECEIVED = 'smtp_server.connection.rcpt_received';

    const CONNECTION_LINE_RECEIVED = 'smtp_server.connection.line_received';

    const CONNECTION_AUTH_ACCEPTED = 'smtp_server.connection.auth_accepted';

    const CONNECTION_AUTH_REFUSED = 'smtp_server.connection.auth_refused';

    const MESSAGE_RECEIVED = 'smtp_server.message.received';
}
