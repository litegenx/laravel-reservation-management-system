# Reservation Management System

[![Build Status](https://travis-ci.com/technote-space/laravel-reservation-management-system.svg?branch=master)](https://travis-ci.com/technote-space/laravel-reservation-management-system)
[![Coverage Status](https://coveralls.io/repos/github/technote-space/laravel-reservation-management-system/badge.svg?branch=master)](https://coveralls.io/github/technote-space/laravel-reservation-management-system?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-space/laravel-reservation-management-system/badge)](https://www.codefactor.io/repository/github/technote-space/laravel-reservation-management-system)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)

[TechCommit](https://www.tech-commit.jp/)

## 概要
ホテルの予約管理システム

## スクリーンショット
### Login
<img src="https://raw.githubusercontent.com/technote-space/laravel-reservation-management-system/images/login.png" width="500px"/>

### Dashboard
<img src="https://raw.githubusercontent.com/technote-space/laravel-reservation-management-system/images/dashboard.png" width="500px"/>

### CRUD
<img src="https://raw.githubusercontent.com/technote-space/laravel-reservation-management-system/images/list.png" width="500px"/>
<img src="https://raw.githubusercontent.com/technote-space/laravel-reservation-management-system/images/edit.png" width="500px"/>

## 要件
- 部屋の管理
- 各部屋の現在の予約状況の確認
- 予約登録
- 利用者の管理
  - 名前/住所/電話番号
- 月毎の売り上げ金額の確認

## 仕様
- 貸出単位は部屋毎
- 1予約者につき1部屋
- 支払いは利用当日に前払い

## データ設計
### 部屋 (rooms)
- 部屋名 (name)
- 最大人数 (number)
- 一泊の金額 (price)
### 利用者 (guests)
### 利用者詳細 (guest_details)
- 利用者ID (guest_id)
- 名前 (name)
- カナ名 (name_kana)
- 住所
  - 郵便番号 (zip_code)
  - 住所 (address)
- 電話番号 (phone)
### 予約 (reservations)
- 利用者ID (guest_id)
- 部屋ID (room_id)
- 利用開始日 (start_date)
- 利用終了日(1泊の場合 = 利用開始日) (end_date)
### 予約詳細 (reservation_details)
- 利用人数 (number)
- 支払金額 (payment)
- 部屋名 (room_name)
- 利用者名 (guest_name)
- 利用者カナ名 (guest_name_kana)
- 利用者郵便番号 (guest_zip_code)
- 利用者住所 (guest_address)
- 利用者電話番号 (guest_phone)
### 管理者 (admins)
- 名前 (name)
- メールアドレス (email)
- パスワード (password)

## 構成
### 言語・フレームワーク
- PHP（Laravel）
  - API サーバとして利用
- JavaScript（Vue.js）
### Lint
- PHP
  - [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer)
  - [PHPMD](https://phpmd.org/)
- JavaScript
  - [ESLint](https://eslint.org/)
- CSS
  - [stylelint](https://github.com/stylelint/stylelint)
### テスト
- PHP
  - [PHPUnit](https://phpunit.de/)（単体テスト）
  - [Laravel Dusk](https://github.com/laravel/dusk)（e2e）
- JavaScript
  - [Jest](https://jestjs.io/)（単体テスト）
  - [Laravel Dusk](https://github.com/laravel/dusk)（e2e）
### CI
- [Travis CI](https://travis-ci.com/)
  - Lint
  - テスト
  - Deploy
    - GitHub Releases
    - GitHub Pages
### デザインフレームワーク
- [Vuetify](https://vuetifyjs.com/)
### その他
- 多言語化
- Vuex, SPA
- [FullCalendar](https://fullcalendar.io/)
- [Chart.js](https://www.chartjs.org/)

## Demonstration
[GitHub Pages](https://technote-space.github.io/laravel-reservation-management-system)  
- ログイン情報
  - email: test@example.com
  - password: test1234
- APIはモックなので実際の動作と差異があります
  - データの並び順
  - データ検索
  - バリデーションなし

[Deployed](https://reservation.technote.space)
 - Basic認証の情報が必要な方はSlack等でお問い合わせください

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)