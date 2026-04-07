<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AttendanceChangeRequestTest extends TestCase
{
    use RefreshDatabase;

//出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される

//休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される

//休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される

//備考欄が未入力の場合のエラーメッセージが表示される

//修正申請処理が実行される

//「承認待ち」にログインユーザーが行った申請が全て表示されていること

//「承認済み」に管理者が承認した修正申請が全て表示されている

//各申請の「詳細」を押下すると勤怠詳細画面に遷移する

}