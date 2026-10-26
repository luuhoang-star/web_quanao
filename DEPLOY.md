# Hướng Dẫn Triển Khai Website Lên Internet (Cloud / PaaS)

Dự án đã được cấu hình đầy đủ file `Dockerfile`, `Nginx`, `Supervisord` và `Entrypoint` tự động hóa cho môi trường Production. Bạn có thể triển khai lên **Render.com** (Hoàn toàn Miễn phí) hoặc **Railway.app**.

---

## 🌟 Phương Án 1: Render.com + TiDB Cloud (Miễn Phí 100% Chạy 24/7)

### Bước 1: Tạo Database MySQL Miễn Phí Trên TiDB Cloud
> Render không có MySQL miễn phí (chỉ có Postgres), vì vậy ta dùng **TiDB Cloud Serverless** (tương thích 100% MySQL, miễn phí 25GB vĩnh viễn, tốc độ cực nhanh).

1. Truy cập [tidbcloud.com](https://tidbcloud.com/) và đăng nhập bằng Google hoặc GitHub.
2. Chọn **Create Cluster** -> Chọn loại **Serverless** (Free).
3. Đặt tên cụm (ví dụ: `quanao-db`) -> Nhấn **Create**.
4. Sau khi tạo xong, màn hình sẽ hiện thông tin kết nối (Connect). Chọn tab **General** hoặc copy:
   - **Host**: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` (ví dụ)
   - **Port**: `4000`
   - **User**: `xxxxxx.root`
   - **Password**: Mật khẩu bạn vừa đặt hoặc TiDB cấp
   - **Database**: `test` (hoặc tạo database `quanao`)

---

### Bước 2: Đẩy Code Mới Lên GitHub
Chạy các lệnh sau trong Terminal dự án:
```bash
git add .
git commit -m "Cấu hình Docker và sẵn sàng deploy lên Cloud"
git push origin main
```

---

### Bước 3: Đưa Web Lên Render.com
1. Truy cập [render.com](https://render.com/) và đăng nhập bằng tài khoản GitHub của bạn.
2. Nhấn nút **New +** ở góc trên cùng bên phải -> Chọn **Web Service**.
3. Chọn kho lưu trữ GitHub của bạn: `luuhoang-star/web_quanao` -> Nhấn **Connect**.
4. Điền các thông tin:
   - **Name**: `shop-quanao` (tùy chọn)
   - **Region**: Chọn `Singapore` (để tốc độ về Việt Nam nhanh nhất)
   - **Language / Environment**: Chọn **Docker**
   - **Instance Type**: Chọn **Free**
5. Cuộn xuống phần **Environment Variables** (Biến môi trường) -> Nhấn **Add Environment Variable** để thêm:

| Key | Value | Ghi chú |
|---|---|---|
| `APP_NAME` | `Shop Quan Ao` | Tên website |
| `APP_ENV` | `production` | Môi trường production |
| `APP_KEY` | `base64:h6vD6qk8djQE2acihzIN0S7dD7hIWH1U5hkUlBToEik=` | Khóa bảo mật |
| `APP_DEBUG` | `false` | Tắt chế độ debug để an toàn |
| `APP_URL` | `https://shop-quanao.onrender.com` | Thay bằng tên miền Render cấp cho bạn |
| `DB_CONNECTION` | `mysql` | Loại CSDL |
| `DB_HOST` | `<host từ TiDB Cloud>` | Ví dụ: `gateway01.us-east-1.prod.aws.tidbcloud.com` |
| `DB_PORT` | `4000` | Port của TiDB Cloud (thường là 4000) |
| `DB_DATABASE` | `test` (hoặc tên db bạn tạo) | Tên database |
| `DB_USERNAME` | `<user từ TiDB Cloud>` | Tên tài khoản |
| `DB_PASSWORD` | `<mật khẩu từ TiDB Cloud>` | Mật khẩu |
| `SESSION_DRIVER` | `file` | Lưu session |
| `CACHE_STORE` | `file` | Lưu cache |
| `QUEUE_CONNECTION` | `sync` | Xử lý queue tức thì |
| `RUN_MIGRATIONS` | `true` | Tự động tạo bảng khi deploy |
| `RUN_SEEDER` | `true` | Tự động nạp dữ liệu mẫu sản phẩm, danh mục |

6. Nhấn **Deploy Web Service**.
7. Chờ khoảng 3 - 5 phút để Render tự động build Docker và khởi động web. Khi hiện trạng thái **Live**, bạn nhấn vào link dạng `https://shop-quanao.onrender.com` để trải nghiệm web trực tiếp trên Internet!

> **Mẹo:** Sau khi deploy thành công lần đầu và dữ liệu đã được tạo xong, bạn có thể chỉnh biến `RUN_SEEDER=false` trong phần Environment của Render để những lần deploy sau không bị chạy lại seeder.

---

## 🚀 Phương Án 2: Railway.app (Cực Nhanh & Đơn Giản)

1. Đăng ký tại [railway.app](https://railway.app/) bằng GitHub.
2. Tạo **New Project** -> Chọn **Provision MySQL** (tạo luôn database MySQL trên Railway).
3. Sau đó nhấn nút **+ New** trong cùng project đó -> Chọn **GitHub Repo** -> Chọn `luuhoang-star/web_quanao`.
4. Trong phần **Variables** của web service, liên kết các biến từ MySQL vừa tạo:
   - `DB_CONNECTION`: `mysql`
   - `DB_HOST`: `${{MySQL.MYSQLHOST}}`
   - `DB_PORT`: `${{MySQL.MYSQLPORT}}`
   - `DB_DATABASE`: `${{MySQL.MYSQLDATABASE}}`
   - `DB_USERNAME`: `${{MySQL.MYSQLUSER}}`
   - `DB_PASSWORD`: `${{MySQL.MYSQLPASSWORD}}`
   - Thêm `APP_KEY`, `APP_ENV=production`, `RUN_MIGRATIONS=true`, `RUN_SEEDER=true`.
5. Vào tab **Settings** của Web Service -> Chọn **Generate Domain** để lấy link truy cập công khai.
