<?php

namespace App\Database;

use Illuminate\Database\Query\Grammars\MySqlGrammar as BaseMySqlGrammar;

class MySqlGrammar extends BaseMySqlGrammar
{
    public function compileThreadCount()
    {
        if ($this->connection->isMaria()) {
            return 'select variable_value as `Value` from information_schema.SESSION_STATUS where variable_name = \'Threads_connected\'';
        }

        return parent::compileThreadCount();
    }
}
