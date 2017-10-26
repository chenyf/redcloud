<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HAVINGBuilder
 *
 * @author Administrator
 */

require_once dirname(__FILE__) . '/../utils/ExpressionType.php';
require_once dirname(__FILE__) . '/../exceptions/UnableToCreateSQLException.php';
require_once dirname(__FILE__) . '/ColumnReferenceBuilder.php';
require_once dirname(__FILE__) . '/ConstantBuilder.php';
require_once dirname(__FILE__) . '/OperatorBuilder.php';
require_once dirname(__FILE__) . '/FunctionBuilder.php';
require_once dirname(__FILE__) . '/InListBuilder.php';
require_once dirname(__FILE__) . '/WhereExpressionBuilder.php';
require_once dirname(__FILE__) . '/WhereBracketExpressionBuilder.php';
require_once dirname(__FILE__) . '/UserVariableBuilder.php';
require_once dirname(__FILE__) . '/SubQueryBuilder.php';

class HavingBuilder {

    protected function buildColRef($parsed) {
        $builder = new ColumnReferenceBuilder();
        return $builder->build($parsed);
    }

    protected function buildConstant($parsed) {
        $builder = new ConstantBuilder();
        return $builder->build($parsed);
    }

    protected function buildOperator($parsed) {
        $builder = new OperatorBuilder();
        return $builder->build($parsed);
    }

    protected function buildFunction($parsed) {
        $builder = new FunctionBuilder();
        return $builder->build($parsed);
    }

    protected function buildSubQuery($parsed) {
        $builder = new SubQueryBuilder();
        return $builder->build($parsed);
    }

    protected function buildInList($parsed) {
        $builder = new InListBuilder();
        return $builder->build($parsed);
    }

    protected function buildWhereExpression($parsed) {
        $builder = new WhereExpressionBuilder();
        return $builder->build($parsed);
    }

    protected function buildWhereBracketExpression($parsed) {
        $builder = new WhereBracketExpressionBuilder();
        return $builder->build($parsed);
    }

    protected function buildUserVariable($parsed) {
        $builder = new UserVariableBuilder();
        return $builder->build($parsed);
    }

    public function build($parsed) {
        $sql = "HAVING ";
        foreach ($parsed as $k => $v) {
            $len = strlen($sql);

            $sql .= $this->buildOperator($v);
            $sql .= $this->buildConstant($v);
            $sql .= $this->buildColRef($v);
            $sql .= $this->buildSubQuery($v);
            $sql .= $this->buildInList($v);
            $sql .= $this->buildFunction($v);
            $sql .= $this->buildWhereExpression($v);
            $sql .= $this->buildWhereBracketExpression($v);
            $sql .= $this->buildUserVariable($v);

            if (strlen($sql) == $len) {
                throw new UnableToCreateSQLException('HAVING', $k, $v, 'expr_type');
            }

            $sql .= " ";
        }
        return substr($sql, 0, -1);
    }

}

?>
