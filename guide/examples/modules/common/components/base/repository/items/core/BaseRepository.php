<?php declare(strict_types=1);

namespace app\common\components\base\repository\items\cote;

use Exception;
use yii\db\{ActiveQuery, Connection};
use app\common\components\base\BaseModelTool;
use app\common\components\base\moels\items\core\BaseModel;
use app\common\components\interfaces\services\core\RepositoryInterface;

/**
 * < Common > Родительский абстрактный класс для всех репозиториев
 *  использующих BaseModel
 *
 * @package app\common\components\base\providers
 *
 * @property BaseModel|string $modelClass
 *
 * @tag: #base #provider
 */
abstract class BaseRepository extends BaseModelTool implements RepositoryInterface
{
    /** @var ?Connection */
    protected ?Connection $connection = null;

    /** @var array Criteria for active items */
    protected array $criteriaActive = [];



    /**
     * Create new find query
     *
     * @param ?mixed $where
     *
     * @return ?ActiveQuery
     *
     * @throws Exception
     */
    public function find( mixed $where = null ): ?ActiveQuery
    {
        try
        {
            $modelClass = $this->getModelClass();

            $query = $modelClass::find();

            if ( $where ) $query->where( $where );

            return $query;

        } catch (Exception $e) {

            $this->handlerCatch( $e, __METHOD__, 'Catch! on `find` item', [
                'where' => $where
            ]);
        }

        return null;
    }

    /**
     * Find active items
     *
     * @param array $where
     *
     * @return ?ActiveQuery
     *
     * @throws Exception
     */
    public function findActive( array $where = [] ): ?ActiveQuery
    {
        try
        {
            $query = $this->find( count($where) ? $where : null );

            if ( count( $this->criteriaActive ) )
            {
                $query->andFilterWhere( $this->criteriaActive );
            }

            return $query;

        } catch (Exception $e) {

            $this->handlerCatch( $e, __METHOD__, 'Catch! on `find active` items', [
                'where' => $where
            ]);
        }

        return null;
    }

    /**
     * @param Connection $connection
     *
     * @return static
     */
    public function setConnection( Connection $connection ): static
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return ?Connection
     */
    public function getConnection(): ?Connection
    {
        return $this->connection;
    }
}