export default {
    title: '予約システム',
    pages: {
        dashboard: 'ダッシュボード',
        guest: '利用者',
        reservation: '予約',
        room: '部屋',
        login: 'ログイン',
        logout: 'ログアウト',
        'not_found': 'Not Found',
    },
    column: {
        id: 'ID',
        name: '名前',
        'room_name': '部屋名',
        katakana: 'カナ名',
        email: 'メールアドレス',
        password: 'パスワード',
        price: '料金',
        number: '人数',
        'max_number': '最大人数',
        'zip_code': '郵便番号',
        address: '住所',
        phone: '電話番号',
        'start_date': '開始日',
        'end_date': '終了日',
        actions: 'アクション',
        guest: '利用者',
        room: '部屋',
    },
    loading: {},
    messages: {
        'password_hint': '{min} 文字以上',
        'delete_item': 'この項目を削除してもよろしいですか？',
        failed: {
            create: '作成に失敗しました。',
            edit: '編集に失敗しました。',
            delete: '削除に失敗しました。',
        },
        succeeded: {
            create: '作成に成功しました。',
            edit: '編集に成功しました。',
            delete: '削除に成功しました。',
        },
    },
    unit: {
        number: '{value}人',
        price: '{value}円',
    },
    hint: {
        'zip_code': '例) 012-3456',
        'phone': '例) 012-3456-7890',
    },
    misc: {
        'new_item': '新規追加',
        'edit_item': '編集',
        confirm: '確認',
        yes: 'はい',
        no: 'いいえ',
        cancel: 'キャンセル',
        ok: 'OK',
        save: '保存',
    },
};
