<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Bridge\Doctrine;

use Doctrine\ORM\Query\SqlWalker;

/**
 * The AclWalker is a TreeWalker that walks over a DQL AST and constructs
 * the corresponding SQL.
 *
 * @link https://gist.github.com/mailaneel/1363377 Original code on gist
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * @author mailaneel
 */
class AclWalker extends SqlWalker
{
    /**
     * {@inheritdoc}
     */
    public function walkFromClause($fromClause)
    {
        $sql = parent::walkFromClause($fromClause);
        $aclMetadata = $this->getQuery()->getHint('acl.metadata');

        if ($aclMetadata) {
            foreach ($aclMetadata as $key => $metadata) {
                $alias = $metadata['alias'];
                $query = $metadata['query'];
                $table = $metadata['table'];
                $tableAlias = $this->getSQLTableAlias($table, $alias);
                $aclAlias = 'ta' . $key . '_';

                $aclSql = <<<ACL_SQL
INNER JOIN ({$query}) {$aclAlias} ON {$tableAlias}.id = {$aclAlias}.id
ACL_SQL;
                $sql .= ' ' . $aclSql;
            }
        }

        return $sql;
    }

}
