<?php
namespace Fone\Mapper;

class UserScoreCache extends AbstractMapper
{

    public function getScores($roundId)
    {
        $rounds = $this->getApp()['roundMapper']->find('id <= ? order by id asc', [$roundId]);
        $users = $this->getApp()['userMapper']->fetchAll();
        $scores = [];
        foreach ($rounds as $round) {
            $thisRoundScores = [];
            foreach ($users as $user) {
                if ($userScoreCache = $this->get(['userId' => $user->getId(), 'roundId' => $round->getId()])) {
                    $userScoreCache->setUser($user);
                    $thisRoundScores[$user->getId()] = $userScoreCache;
                } else {
                    $userTeam = $this->getApp()['userTeamMapper']->getTeamForRound($user->getId(), $round->getId());
                    if ($userTeam) {
                        $score = $userTeam->getScoreForRound($round->getId());
                        $lastRoundTotal = (isset($scores[$round->getId() -1][$user->getId()]))? $scores[$round->getId() -1][$user->getId()]->getTotalScore() : 0;
                        $cacheModel = $this->_hydrate([
                            'userId' => $user->getId(),
                            'roundId' => $round->getId(),
                            'roundScore' => $score,
                            'totalScore' => $lastRoundTotal + $score
                        ]);
                        $cacheModel->setUser($user);
                        $thisRoundScores[$user->getId()] = $cacheModel;
                        $this->save($cacheModel);
                    }
                }
                usort($thisRoundScores, function ($a, $b) {
                    if ($a->getTotalScore() ==  $b->getTotalScore()) return 0;
                    return ($a->getTotalScore() < $b->getTotalScore()) ? 1 : -1;
                });
            }
            $scores[$round->getId()] = ['round' => $round, 'scores' => $thisRoundScores];
        }
        return $scores;
    }


    protected function _hydrate($row)
    {
        $model = parent::_hydrate($row);
        //For some reason this is causing php to hit max memory (without it
        // it's nowhere near), a werid bug I have no idea where it's coming from.
        /*if ($model) {
            $app = $this->getApp();
            $callback = function ($userScoreCacheModel) use ($app) {
                return $app['userMapper']->get($userScoreCacheModel->getUserId());
            };
            $model->addReference('drivers', $callback);
        }*/
        return $model;
    }

    public function getDriverFromCacheModel($model)
    {

    }
}