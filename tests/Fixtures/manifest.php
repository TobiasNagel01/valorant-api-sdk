<?php

declare(strict_types=1);
use Tobiasn\ValorantApi\DataTransferObjects\Account\AccountDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Content\ContentDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsEventDetailDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsEventDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsMatchDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsPlayerDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsPlayerMatchDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsTeamDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsTeamMatchDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Esports\EsportsTeamTransactionDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Game\QueueStatusDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Game\StatusDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Game\VersionDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Leaderboard\LeaderboardDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Matches\MatchesDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Mmr\MMRDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Mmr\MMRHistoryDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premier\PremierTeamHistoryResponseDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premier\PremierTeamLiteResponseDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premier\PremierTeamResponseDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premium\PremiumWebhookDeleteDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premium\PremiumWebhookUserAddRequestDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premium\PremiumWebhookUserMutationDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Premium\PremiumWebhookUserUpdateRequestDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Raw\RawPayloadDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Store\StoreFeaturedDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Store\StoreOffersDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Stored\StoredMatchDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Stored\StoredMMRDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Website\WebsiteByIdDataDTO;
use Tobiasn\ValorantApi\DataTransferObjects\Website\WebsiteDataDTO;
use Tobiasn\ValorantApi\Requests\Account\GetAccountByPuuidRequest;
use Tobiasn\ValorantApi\Requests\Account\GetAccountRequest;
use Tobiasn\ValorantApi\Requests\Content\GetContentRequest;
use Tobiasn\ValorantApi\Requests\Crosshair\GenerateCrosshairRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsEventMatchesRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsEventsRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsMatchRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsPlayerMatchesRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsPlayerRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsScheduleRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsTeamMatchesRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsTeamRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsTeamTransactionsRequest;
use Tobiasn\ValorantApi\Requests\Game\GetQueueStatusRequest;
use Tobiasn\ValorantApi\Requests\Game\GetStatusRequest;
use Tobiasn\ValorantApi\Requests\Game\GetVersionRequest;
use Tobiasn\ValorantApi\Requests\Leaderboard\GetLeaderboardRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetMatchesByPuuidRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetMatchesRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetMatchRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetStoredMatchesByPuuidRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetStoredMatchesRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetMmrByPuuidRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetMmrHistoryByPuuidRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetMmrHistoryRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetMmrRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetStoredMmrHistoryByPuuidRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetStoredMmrHistoryRequest;
use Tobiasn\ValorantApi\Requests\Premier\GetPremierLeaderboardRequest;
use Tobiasn\ValorantApi\Requests\Premier\GetPremierTeamByIdRequest;
use Tobiasn\ValorantApi\Requests\Premier\GetPremierTeamHistoryByIdRequest;
use Tobiasn\ValorantApi\Requests\Premier\GetPremierTeamHistoryRequest;
use Tobiasn\ValorantApi\Requests\Premier\GetPremierTeamRequest;
use Tobiasn\ValorantApi\Requests\Premier\SearchPremierTeamsRequest;
use Tobiasn\ValorantApi\Requests\Premium\AddWebhookUserRequest;
use Tobiasn\ValorantApi\Requests\Premium\DeleteWebhookUserRequest;
use Tobiasn\ValorantApi\Requests\Premium\GetWebhookSettingsRequest;
use Tobiasn\ValorantApi\Requests\Premium\UpdateWebhookUserRequest;
use Tobiasn\ValorantApi\Requests\Raw\GetRawDataRequest;
use Tobiasn\ValorantApi\Requests\Store\GetStoreFeaturedRequest;
use Tobiasn\ValorantApi\Requests\Store\GetStoreOffersRequest;
use Tobiasn\ValorantApi\Requests\Website\GetWebsiteArticleRequest;
use Tobiasn\ValorantApi\Requests\Website\GetWebsiteArticlesRequest;

/**
 * Generated by bin/generate.php. Do not edit by hand.
 *
 * One entry per operation: a sample response body, the DTO it should hydrate into, and a
 * factory for the request itself.
 */
return [
    'get_account_v2' => [
        'group' => 'Account',
        'fixture' => __DIR__.'/get_account_v2.json',
        'status' => 200,
        'dto' => AccountDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetAccountRequest => new GetAccountRequest('sample', 'sample'),
    ],
    'get_account_by_id_v2' => [
        'group' => 'Account',
        'fixture' => __DIR__.'/get_account_by_id_v2.json',
        'status' => 200,
        'dto' => AccountDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetAccountByPuuidRequest => new GetAccountByPuuidRequest('sample'),
    ],
    'get_mmr_v3_by_name' => [
        'group' => 'Mmr',
        'fixture' => __DIR__.'/get_mmr_v3_by_name.json',
        'status' => 200,
        'dto' => MMRDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetMmrRequest => new GetMmrRequest('eu', 'pc', 'sample', 'sample'),
    ],
    'get_mmr_v3_by_id' => [
        'group' => 'Mmr',
        'fixture' => __DIR__.'/get_mmr_v3_by_id.json',
        'status' => 200,
        'dto' => MMRDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetMmrByPuuidRequest => new GetMmrByPuuidRequest('eu', 'pc', 'sample'),
    ],
    'get_mmr_history_v2_by_name' => [
        'group' => 'Mmr',
        'fixture' => __DIR__.'/get_mmr_history_v2_by_name.json',
        'status' => 200,
        'dto' => MMRHistoryDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetMmrHistoryRequest => new GetMmrHistoryRequest('eu', 'pc', 'sample', 'sample'),
    ],
    'get_mmr_history_v2_by_id' => [
        'group' => 'Mmr',
        'fixture' => __DIR__.'/get_mmr_history_v2_by_id.json',
        'status' => 200,
        'dto' => MMRHistoryDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetMmrHistoryByPuuidRequest => new GetMmrHistoryByPuuidRequest('eu', 'pc', 'sample'),
    ],
    'stored_mmr_history_v2' => [
        'group' => 'Mmr',
        'fixture' => __DIR__.'/stored_mmr_history_v2.json',
        'status' => 200,
        'dto' => StoredMMRDTO::class,
        'collection' => true,
        'request' => static fn (): GetStoredMmrHistoryRequest => new GetStoredMmrHistoryRequest('eu', 'pc', 'sample', 'sample'),
    ],
    'stored_mmr_history_v2_by_id' => [
        'group' => 'Mmr',
        'fixture' => __DIR__.'/stored_mmr_history_v2_by_id.json',
        'status' => 200,
        'dto' => StoredMMRDTO::class,
        'collection' => true,
        'request' => static fn (): GetStoredMmrHistoryByPuuidRequest => new GetStoredMmrHistoryByPuuidRequest('eu', 'pc', 'sample'),
    ],
    'get_matches_v4_by_name' => [
        'group' => 'Matches',
        'fixture' => __DIR__.'/get_matches_v4_by_name.json',
        'status' => 200,
        'dto' => MatchesDataDTO::class,
        'collection' => true,
        'request' => static fn (): GetMatchesRequest => new GetMatchesRequest('eu', 'pc', 'sample', 'sample'),
    ],
    'get_matches_v4_by_id' => [
        'group' => 'Matches',
        'fixture' => __DIR__.'/get_matches_v4_by_id.json',
        'status' => 200,
        'dto' => MatchesDataDTO::class,
        'collection' => true,
        'request' => static fn (): GetMatchesByPuuidRequest => new GetMatchesByPuuidRequest('eu', 'pc', 'sample'),
    ],
    'match_v4' => [
        'group' => 'Matches',
        'fixture' => __DIR__.'/match_v4.json',
        'status' => 200,
        'dto' => MatchesDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetMatchRequest => new GetMatchRequest('eu', 'sample'),
    ],
    'stored_matches' => [
        'group' => 'Matches',
        'fixture' => __DIR__.'/stored_matches.json',
        'status' => 200,
        'dto' => StoredMatchDTO::class,
        'collection' => true,
        'request' => static fn (): GetStoredMatchesRequest => new GetStoredMatchesRequest('eu', 'sample', 'sample'),
    ],
    'stored_matches_by_id' => [
        'group' => 'Matches',
        'fixture' => __DIR__.'/stored_matches_by_id.json',
        'status' => 200,
        'dto' => StoredMatchDTO::class,
        'collection' => true,
        'request' => static fn (): GetStoredMatchesByPuuidRequest => new GetStoredMatchesByPuuidRequest('eu', 'sample'),
    ],
    'leaderboard_v3' => [
        'group' => 'Leaderboard',
        'fixture' => __DIR__.'/leaderboard_v3.json',
        'status' => 200,
        'dto' => LeaderboardDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetLeaderboardRequest => new GetLeaderboardRequest('eu', 'pc'),
    ],
    'premier_search' => [
        'group' => 'Premier',
        'fixture' => __DIR__.'/premier_search.json',
        'status' => 200,
        'dto' => PremierTeamLiteResponseDataDTO::class,
        'collection' => true,
        'request' => static fn (): SearchPremierTeamsRequest => new SearchPremierTeamsRequest,
    ],
    'premier_leaderboard' => [
        'group' => 'Premier',
        'fixture' => __DIR__.'/premier_leaderboard.json',
        'status' => 200,
        'dto' => PremierTeamLiteResponseDataDTO::class,
        'collection' => true,
        'request' => static fn (): GetPremierLeaderboardRequest => new GetPremierLeaderboardRequest('eu'),
    ],
    'premier_by_name' => [
        'group' => 'Premier',
        'fixture' => __DIR__.'/premier_by_name.json',
        'status' => 200,
        'dto' => PremierTeamResponseDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetPremierTeamRequest => new GetPremierTeamRequest('sample', 'sample'),
    ],
    'premier_by_id' => [
        'group' => 'Premier',
        'fixture' => __DIR__.'/premier_by_id.json',
        'status' => 200,
        'dto' => PremierTeamResponseDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetPremierTeamByIdRequest => new GetPremierTeamByIdRequest('sample'),
    ],
    'premier_by_name_history' => [
        'group' => 'Premier',
        'fixture' => __DIR__.'/premier_by_name_history.json',
        'status' => 200,
        'dto' => PremierTeamHistoryResponseDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetPremierTeamHistoryRequest => new GetPremierTeamHistoryRequest('sample', 'sample'),
    ],
    'premier_by_id_history' => [
        'group' => 'Premier',
        'fixture' => __DIR__.'/premier_by_id_history.json',
        'status' => 200,
        'dto' => PremierTeamResponseDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetPremierTeamHistoryByIdRequest => new GetPremierTeamHistoryByIdRequest('sample'),
    ],
    'esports_schedules_v1' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_schedules_v1.json',
        'status' => 200,
        'dto' => EsportsDataDTO::class,
        'collection' => true,
        'request' => static fn (): GetEsportsScheduleRequest => new GetEsportsScheduleRequest,
    ],
    'esports_events_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_events_v2.json',
        'status' => 200,
        'dto' => EsportsEventDTO::class,
        'collection' => true,
        'request' => static fn (): GetEsportsEventsRequest => new GetEsportsEventsRequest,
    ],
    'esports_event_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_event_v2.json',
        'status' => 200,
        'dto' => EsportsEventDetailDTO::class,
        'collection' => true,
        'request' => static fn (): GetEsportsEventMatchesRequest => new GetEsportsEventMatchesRequest(1),
    ],
    'esports_match_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_match_v2.json',
        'status' => 200,
        'dto' => EsportsMatchDTO::class,
        'collection' => false,
        'request' => static fn (): GetEsportsMatchRequest => new GetEsportsMatchRequest(1),
    ],
    'esports_team_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_team_v2.json',
        'status' => 200,
        'dto' => EsportsTeamDTO::class,
        'collection' => false,
        'request' => static fn (): GetEsportsTeamRequest => new GetEsportsTeamRequest(1),
    ],
    'esports_team_matches_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_team_matches_v2.json',
        'status' => 200,
        'dto' => EsportsTeamMatchDTO::class,
        'collection' => true,
        'request' => static fn (): GetEsportsTeamMatchesRequest => new GetEsportsTeamMatchesRequest(1),
    ],
    'esports_team_transactions_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_team_transactions_v2.json',
        'status' => 200,
        'dto' => EsportsTeamTransactionDTO::class,
        'collection' => true,
        'request' => static fn (): GetEsportsTeamTransactionsRequest => new GetEsportsTeamTransactionsRequest(1),
    ],
    'esports_player_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_player_v2.json',
        'status' => 200,
        'dto' => EsportsPlayerDTO::class,
        'collection' => false,
        'request' => static fn (): GetEsportsPlayerRequest => new GetEsportsPlayerRequest(1),
    ],
    'esports_player_matches_v2' => [
        'group' => 'Esports',
        'fixture' => __DIR__.'/esports_player_matches_v2.json',
        'status' => 200,
        'dto' => EsportsPlayerMatchDTO::class,
        'collection' => true,
        'request' => static fn (): GetEsportsPlayerMatchesRequest => new GetEsportsPlayerMatchesRequest(1),
    ],
    'get_content_v1' => [
        'group' => 'Content',
        'fixture' => __DIR__.'/get_content_v1.json',
        'status' => 200,
        'dto' => ContentDTO::class,
        'collection' => false,
        'request' => static fn (): GetContentRequest => new GetContentRequest,
    ],
    'StoreFeatured' => [
        'group' => 'Store',
        'fixture' => __DIR__.'/StoreFeatured.json',
        'status' => 200,
        'dto' => StoreFeaturedDTO::class,
        'collection' => false,
        'request' => static fn (): GetStoreFeaturedRequest => new GetStoreFeaturedRequest('v1'),
    ],
    'StoreOffers' => [
        'group' => 'Store',
        'fixture' => __DIR__.'/StoreOffers.json',
        'status' => 200,
        'dto' => StoreOffersDTO::class,
        'collection' => false,
        'request' => static fn (): GetStoreOffersRequest => new GetStoreOffersRequest('v1'),
    ],
    'crosshair' => [
        'group' => 'Crosshair',
        'fixture' => __DIR__.'/crosshair.json',
        'status' => 200,
        'dto' => null,
        'collection' => false,
        'request' => static fn (): GenerateCrosshairRequest => new GenerateCrosshairRequest,
    ],
    'Status' => [
        'group' => 'Game',
        'fixture' => __DIR__.'/Status.json',
        'status' => 200,
        'dto' => StatusDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetStatusRequest => new GetStatusRequest('eu'),
    ],
    'QueueStatus' => [
        'group' => 'Game',
        'fixture' => __DIR__.'/QueueStatus.json',
        'status' => 200,
        'dto' => QueueStatusDataDTO::class,
        'collection' => true,
        'request' => static fn (): GetQueueStatusRequest => new GetQueueStatusRequest('eu'),
    ],
    'Version' => [
        'group' => 'Game',
        'fixture' => __DIR__.'/Version.json',
        'status' => 200,
        'dto' => VersionDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetVersionRequest => new GetVersionRequest('eu'),
    ],
    'Website' => [
        'group' => 'Website',
        'fixture' => __DIR__.'/Website.json',
        'status' => 200,
        'dto' => WebsiteDataDTO::class,
        'collection' => true,
        'request' => static fn (): GetWebsiteArticlesRequest => new GetWebsiteArticlesRequest('sample'),
    ],
    'WebsiteById' => [
        'group' => 'Website',
        'fixture' => __DIR__.'/WebsiteById.json',
        'status' => 200,
        'dto' => WebsiteByIdDataDTO::class,
        'collection' => false,
        'request' => static fn (): GetWebsiteArticleRequest => new GetWebsiteArticleRequest('sample', 'sample'),
    ],
    'Raw' => [
        'group' => 'Raw',
        'fixture' => __DIR__.'/Raw.json',
        'status' => 200,
        'dto' => null,
        'collection' => false,
        'request' => static fn (): GetRawDataRequest => new GetRawDataRequest(RawPayloadDTO::from([
            'platform' => null,
            'queries' => null,
            'region' => 'sample',
            'type' => 'sample',
            'value' => 'sample',
        ])),
    ],
    'get_webhook_settings' => [
        'group' => 'Premium',
        'fixture' => __DIR__.'/get_webhook_settings.json',
        'status' => 200,
        'dto' => null,
        'collection' => false,
        'request' => static fn (): GetWebhookSettingsRequest => new GetWebhookSettingsRequest,
    ],
    'add_webhook_user' => [
        'group' => 'Premium',
        'fixture' => __DIR__.'/add_webhook_user.json',
        'status' => 201,
        'dto' => PremiumWebhookUserMutationDataDTO::class,
        'collection' => false,
        'request' => static fn (): AddWebhookUserRequest => new AddWebhookUserRequest(PremiumWebhookUserAddRequestDTO::from([
            'enabled' => null,
            'events' => null,
            'name' => null,
            'puuid' => null,
            'tag' => null,
        ])),
    ],
    'update_webhook_user' => [
        'group' => 'Premium',
        'fixture' => __DIR__.'/update_webhook_user.json',
        'status' => 200,
        'dto' => null,
        'collection' => false,
        'request' => static fn (): UpdateWebhookUserRequest => new UpdateWebhookUserRequest('sample', PremiumWebhookUserUpdateRequestDTO::from([
            'events' => null,
        ])),
    ],
    'delete_webhook_user' => [
        'group' => 'Premium',
        'fixture' => __DIR__.'/delete_webhook_user.json',
        'status' => 200,
        'dto' => PremiumWebhookDeleteDataDTO::class,
        'collection' => false,
        'request' => static fn (): DeleteWebhookUserRequest => new DeleteWebhookUserRequest('sample'),
    ],
];
