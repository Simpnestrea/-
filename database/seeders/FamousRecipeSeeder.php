<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Support\Str;

class FamousRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $categories = [
            'Món Nước' => Category::firstOrCreate(['slug' => 'mon-nuoc'], ['name' => 'Món Nước', 'image' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?w=400']),
            'Món Khô'  => Category::firstOrCreate(['slug' => 'mon-kho'],  ['name' => 'Món Khô',  'image' => 'https://images.unsplash.com/photo-1618040996337-56904b7850b9?w=400']),
            'Món Bánh' => Category::firstOrCreate(['slug' => 'mon-banh'], ['name' => 'Món Bánh', 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400']),
            'Món Cuốn' => Category::firstOrCreate(['slug' => 'mon-cuon'], ['name' => 'Món Cuốn', 'image' => 'https://images.unsplash.com/photo-1550505096-7bbca2955f9a?w=400']),
            'Món Cơm'  => Category::firstOrCreate(['slug' => 'mon-com'],  ['name' => 'Món Cơm',  'image' => 'https://images.unsplash.com/photo-1621508216335-b2fb3620f4c0?w=400']),
        ];

        $recipes = [
            // ===================== PHỞ BÒ =====================
            [
                'title'       => 'Phở Bò Gia Truyền',
                'description' => 'Món phở bò mang đậm hương vị truyền thống Hà Nội, nước dùng thanh ngọt từ xương bò ninh kỹ cùng các loại thảo mộc.',
                'time_to_cook'=> 240,
                'difficulty'  => 'khó',
                'image'       => 'https://images.unsplash.com/photo-1582878826629-29b7ad1ccd63?w=800',
                'category'    => 'Món Nước',
                'tips'        => 'Ninh xương ít nhất 4 tiếng để nước dùng ngọt thanh. Bắt buộc phải nướng gừng và hành khô trước khi cho vào nồi.',
                'ingredients' => [
                    ['name' => 'Xương ống bò',        'quantity' => '1.5',  'unit' => 'kg'],
                    ['name' => 'Thịt bò thăn/gầu',    'quantity' => '500',  'unit' => 'g'],
                    ['name' => 'Bánh phở tươi',        'quantity' => '1',    'unit' => 'kg'],
                    ['name' => 'Gừng tươi',            'quantity' => '80',   'unit' => 'g'],
                    ['name' => 'Hành tây khô',         'quantity' => '3',    'unit' => 'củ'],
                    ['name' => 'Hoa hồi',              'quantity' => '5',    'unit' => 'cánh'],
                    ['name' => 'Quế chi',              'quantity' => '1',    'unit' => 'thanh 5cm'],
                    ['name' => 'Thảo quả',             'quantity' => '2',    'unit' => 'quả'],
                    ['name' => 'Đinh hương',           'quantity' => '4',    'unit' => 'nụ'],
                    ['name' => 'Hạt ngò',              'quantity' => '1',    'unit' => 'muỗng cà phê'],
                    ['name' => 'Nước mắm ngon',        'quantity' => '3',    'unit' => 'muỗng canh'],
                    ['name' => 'Muối',                 'quantity' => '1',    'unit' => 'muỗng canh'],
                    ['name' => 'Đường phèn',           'quantity' => '20',   'unit' => 'g'],
                ],
                'steps' => [
                    '**Sơ chế xương bò:** Cho 1.5 kg xương ống vào nồi, đổ nước lạnh ngập xương, đun sôi mạnh 5 phút. Vớt xương ra, rửa sạch từng cái dưới vòi nước lạnh để loại bỏ hoàn toàn máu và cặn bẩn. Đây là bước quan trọng giúp nước dùng trong và không bị hôi.',
                    '**Nướng thơm gia vị:** Đặt 80g gừng (cả vỏ) và 3 củ hành khô trực tiếp lên ngọn lửa gas, nướng xoay đều 8–10 phút đến khi vỏ ngoài cháy xém, bên trong mềm thơm. Bóc vỏ cháy, đập dập gừng. Song song, rang khô trong chảo khô ở lửa nhỏ: 5 cánh hoa hồi + 1 thanh quế + 2 quả thảo quả đập vỡ + 4 nụ đinh hương + 1 mcf hạt ngò trong 3 phút cho đến khi thơm. Cho tất cả vào túi vải lọc gia vị.',
                    '**Ninh nước dùng:** Cho xương sạch vào nồi lớn, đổ 5 lít nước lạnh. Đun sôi rồi hớt bọt liên tục 10 phút đầu. Thêm gừng, hành đã nướng và túi gia vị. Giảm lửa liu riu, ninh 4–6 tiếng không đậy vung. Sau 3 tiếng nêm: 3 mcb nước mắm + 1 mcb muối + 20g đường phèn. Nếm thử – nước dùng đạt khi có vị ngọt thanh, mùi thơm của hoa hồi, nước trong ánh vàng.',
                    '**Chuẩn bị thịt tái:** Cho 500g thịt thăn bò vào ngăn đông 30 phút cho se lại. Thái lát thật mỏng 2–3mm, cắt ngược chiều thớ thịt để thịt mềm. Xếp ra đĩa, bọc màng thực phẩm, giữ trong ngăn mát đến khi dùng.',
                    '**Trụng bánh phở:** Đun riêng một nồi nước đến sôi sùng sục. Mỗi phần lấy 150–200g bánh phở, cho vào rổ nhỏ, nhúng vào nước sôi 15–20 giây, dùng đũa gỡ nhẹ. Vớt ngay ra tô. Không trụng lâu hơn – bánh sẽ bị nát.',
                    '**Hoàn thiện và trình bày:** Xếp thịt bò tái lên mặt bánh phở trong tô. Thêm hành lá cắt khúc 2cm + hành tây thái nhẫn mỏng + 1 nhúm ngò gai. Múc nước dùng đang sôi bùng chan thật mạnh và nhiều vào tô để thịt chín ngay lập tức. Ăn kèm: giá trụng, húng quế, chanh tươi, ớt và tương đen/đỏ.',
                ],
            ],

            // ===================== BÁNH MÌ =====================
            [
                'title'       => 'Bánh Mì Thịt Nướng',
                'description' => 'Bánh mì giòn rụm kẹp thịt nướng sả thơm lừng, kết hợp với đồ chua và pate béo ngậy.',
                'time_to_cook'=> 45,
                'difficulty'  => 'trung bình',
                'image'       => 'https://images.unsplash.com/photo-1662919374026-64c8bd322dfc?w=800',
                'category'    => 'Món Bánh',
                'tips'        => 'Ướp thịt qua đêm trong tủ lạnh để gia vị thấm đều và thịt mềm hơn khi nướng.',
                'ingredients' => [
                    ['name' => 'Thịt nạc vai heo',           'quantity' => '500',     'unit' => 'g'],
                    ['name' => 'Bánh mì baguette',            'quantity' => '4',       'unit' => 'ổ'],
                    ['name' => 'Sả băm nhuyễn',               'quantity' => '30',      'unit' => 'g (~3 cây)'],
                    ['name' => 'Tỏi băm nhuyễn',              'quantity' => '15',      'unit' => 'g (~4 tép)'],
                    ['name' => 'Nước mắm',                    'quantity' => '2',       'unit' => 'muỗng canh'],
                    ['name' => 'Dầu hào',                     'quantity' => '1.5',     'unit' => 'muỗng canh'],
                    ['name' => 'Mật ong',                     'quantity' => '1',       'unit' => 'muỗng canh'],
                    ['name' => 'Ngũ vị hương',                'quantity' => '0.5',     'unit' => 'muỗng cà phê'],
                    ['name' => 'Tiêu xay',                    'quantity' => '0.5',     'unit' => 'muỗng cà phê'],
                    ['name' => 'Pate gan',                    'quantity' => '100',     'unit' => 'g'],
                    ['name' => 'Bơ lạt',                      'quantity' => '20',      'unit' => 'g'],
                    ['name' => 'Cà rốt + củ cải (đồ chua)',  'quantity' => '150+150', 'unit' => 'g'],
                    ['name' => 'Giấm trắng + đường (đồ chua)','quantity' => '3+2',    'unit' => 'muỗng canh'],
                ],
                'steps' => [
                    '**Làm đồ chua (trước 1–2 tiếng):** Cà rốt và củ cải bào sợi mỏng ~2mm, ướp với 1 mcf muối trong 10 phút rồi vắt ráo. Pha hỗn hợp ngâm: 3 mcb giấm + 2 mcb đường + 150ml nước ấm, khuấy cho tan. Ngâm rau ít nhất 1 tiếng. Đồ chua đạt khi ăn giòn, có vị chua ngọt dịu.',
                    '**Ướp thịt:** Thái thịt nạc vai lát dày 5–7mm. Trộn hỗn hợp ướp: 30g sả băm + 15g tỏi băm + 2 mcb nước mắm + 1.5 mcb dầu hào + 1 mcb mật ong + 0.5 mcf ngũ vị hương + 0.5 mcf tiêu xay. Trộn đều với thịt, bọc màng thực phẩm, ướp tối thiểu 1 tiếng (qua đêm tốt nhất).',
                    '**Nướng thịt:** Làm nóng nồi chiên không dầu 185°C trong 5 phút. Xếp thịt 1 lớp, nướng 12 phút. Lật mặt, phết thêm mật ong nguyên chất lên trên, nướng tiếp 6–8 phút đến khi cạnh thịt cháy vàng nâu đẹp. Nướng than: lửa vừa, trở đều 2–3 phút/lần.',
                    '**Chuẩn bị bánh:** Rạch dọc ổ bánh mì, không cắt đứt. Phết ~5g bơ lạt vào hai mặt trong, tiếp đó phết 25g pate gan đều. Cho vào lò nướng 180°C trong 3–4 phút đến khi vỏ giòn lại và bơ tan thấm vào bánh.',
                    '**Nhồi bánh và hoàn thiện:** Xếp vào ổ theo thứ tự: vài lá ngò rí → 3–4 lát dưa leo cắt dọc → 4–5 lát thịt nướng → 1 mcb đồ chua vắt ráo → 1–2 lát ớt tươi. Chan vài giọt xì dầu lên toàn bộ nhân. Ăn ngay khi bánh còn nóng giòn.',
                ],
            ],

            // ===================== GỎI CUỐN =====================
            [
                'title'       => 'Gỏi Cuốn Tôm Thịt',
                'description' => 'Món ăn thanh mát với tôm luộc, thịt ba chỉ, bún tươi và rau sống cuốn trong lớp bánh tráng mỏng.',
                'time_to_cook'=> 30,
                'difficulty'  => 'dễ',
                'image'       => 'https://images.unsplash.com/photo-1550505096-7bbca2955f9a?w=800',
                'category'    => 'Món Cuốn',
                'tips'        => 'Nhúng bánh tráng bằng nước ấm 50°C, không dùng nước sôi. Cuốn chặt tay để cuốn đẹp và không bị bung.',
                'ingredients' => [
                    ['name' => 'Tôm sú tươi',        'quantity' => '300', 'unit' => 'g (~12 con)'],
                    ['name' => 'Thịt ba chỉ',         'quantity' => '300', 'unit' => 'g'],
                    ['name' => 'Bún tươi sợi nhỏ',   'quantity' => '300', 'unit' => 'g'],
                    ['name' => 'Bánh tráng mỏng',     'quantity' => '12',  'unit' => 'tờ 22cm'],
                    ['name' => 'Xà lách',              'quantity' => '100', 'unit' => 'g'],
                    ['name' => 'Giá đỗ',               'quantity' => '80',  'unit' => 'g'],
                    ['name' => 'Húng quế + húng lủi', 'quantity' => '30',  'unit' => 'g mỗi loại'],
                    ['name' => 'Hẹ tươi',              'quantity' => '12',  'unit' => 'nhánh dài'],
                    ['name' => 'Gừng tươi (luộc tôm)','quantity' => '20',  'unit' => 'g'],
                    ['name' => 'Tương đen (hoisin)',   'quantity' => '4',   'unit' => 'muỗng canh'],
                    ['name' => 'Bơ đậu phộng mịn',    'quantity' => '2',   'unit' => 'muỗng canh'],
                    ['name' => 'Đường + nước chanh',   'quantity' => '1+1', 'unit' => 'muỗng cà phê'],
                ],
                'steps' => [
                    '**Luộc thịt:** Cho 300g thịt ba chỉ vào nồi, đổ nước lạnh vừa ngập. Thêm 1 mcf muối + vài lát gừng. Đun sôi, giảm lửa vừa, luộc 22–25 phút (xiên tăm không thấy nước đỏ). Vớt ra ngâm ngay tô nước đá lạnh 5 phút để thịt săn giòn. Thái lát mỏng 3mm, mỗi cuốn dùng 2–3 lát.',
                    '**Luộc tôm:** Đun sôi nồi nước, thêm 20g gừng đập dập + 1 mcf muối. Cho tôm vào luộc 3–4 phút đến khi cong tròn và chuyển màu hồng cam. Vớt ra ngâm ngay nước đá. Bóc vỏ, giữ đuôi, rạch sống lưng, chẻ đôi tôm ra thành 2 nửa phẳng.',
                    '**Sơ chế rau và bún:** Rau thơm nhặt lấy lá, ngâm nước muối loãng (0.5 mcf muối/lít nước) 5 phút, vớt ra để ráo. Giá đỗ trụng nước sôi 20 giây vớt ra. Bún chần nước sôi 1 phút, xả nước lạnh cho tơi, chia thành từng phần ~30g.',
                    '**Pha nước chấm tương đen:** Trộn: 4 mcb tương đen (hoisin) + 2 mcb bơ đậu phộng + 1 mcf đường + 1 mcf nước cốt chanh + 2 mcb nước ấm. Khuấy đến khi mịn sánh. Đổ ra chén nhỏ, rắc đậu phộng rang đập nhỏ lên trên.',
                    '**Cuốn gỏi:** Đổ nước ấm 50–55°C ra đĩa sâu. Nhúng 1 tờ bánh tráng 3–4 giây, xoay đều cho mềm đồng đều, trải ra thớt. Xếp nhân: xà lách → rau thơm → 30g bún → giá → 2–3 lát thịt → 2 nửa tôm (màu hồng hướng ra ngoài) → 1 nhánh hẹ dài. Gấp 2 mép trái phải vào trước, cuộn chặt từ dưới lên. Tôm và hẹ phải nhìn thấy rõ qua lớp bánh.',
                ],
            ],

            // ===================== CƠM TẤM =====================
            [
                'title'       => 'Cơm Tấm Sườn Bì Chả',
                'description' => 'Đặc sản Sài Gòn với dĩa cơm tấm thơm lừng, sườn nướng mỡ hành tươm mỡ và miếng chả trứng béo ngậy.',
                'time_to_cook'=> 90,
                'difficulty'  => 'trung bình',
                'image'       => 'https://images.unsplash.com/photo-1621508216335-b2fb3620f4c0?w=800',
                'category'    => 'Món Cơm',
                'tips'        => 'Nước mắm chan cơm tấm phải kẹo sánh, ngọt nhiều hơn mặn. Sườn ướp qua đêm thì mới ngon đúng điệu.',
                'ingredients' => [
                    ['name' => 'Sườn cốt lết heo',      'quantity' => '4',     'unit' => 'miếng (~600g)'],
                    ['name' => 'Gạo tấm',                'quantity' => '400',   'unit' => 'g'],
                    ['name' => 'Thịt nạc vai xay',       'quantity' => '200',   'unit' => 'g'],
                    ['name' => 'Trứng vịt',               'quantity' => '4',     'unit' => 'quả'],
                    ['name' => 'Bì heo (da heo sợi)',    'quantity' => '150',   'unit' => 'g'],
                    ['name' => 'Sả băm nhuyễn',           'quantity' => '40',    'unit' => 'g (~4 cây)'],
                    ['name' => 'Tỏi băm',                 'quantity' => '20',    'unit' => 'g (~6 tép)'],
                    ['name' => 'Dầu hào',                 'quantity' => '3',     'unit' => 'muỗng canh'],
                    ['name' => 'Nước mắm',                'quantity' => '4',     'unit' => 'muỗng canh'],
                    ['name' => 'Mật ong',                 'quantity' => '1.5',   'unit' => 'muỗng canh'],
                    ['name' => 'Đường',                   'quantity' => '3',     'unit' => 'muỗng canh'],
                    ['name' => 'Hành lá',                 'quantity' => '4',     'unit' => 'cây'],
                    ['name' => 'Mỡ nước',                 'quantity' => '2',     'unit' => 'muỗng canh'],
                ],
                'steps' => [
                    '**Ướp sườn:** Dùng búa đập nhẹ hai mặt sườn cốt lết để thớ thịt mềm. Trộn hỗn hợp ướp: 40g sả băm + 20g tỏi băm + 3 mcb dầu hào + 2 mcb nước mắm + 1.5 mcb mật ong + 1 mcb đường + 0.5 mcf tiêu xay. Xoa đều lên cả hai mặt, bọc màng, để ngăn mát tối thiểu 2 tiếng (qua đêm càng tốt).',
                    '**Nấu cơm tấm:** Vo gạo tấm 3 lần đến khi nước trong. Nấu tỷ lệ 1 chén gạo : 1.1 chén nước (ít hơn cơm thường để hạt tơi xốp). Khi cơm vừa cạn nước, giảm lửa nhỏ nhất, đậy vung, ủ thêm 10 phút sau khi tắt bếp.',
                    '**Làm chả trứng hấp:** Ngâm 20g miến tàu + 10g nấm mèo khô trong nước ấm 15 phút, thái nhỏ. Trộn đều: 200g thịt xay + miến + nấm + 2 lòng trắng trứng vịt + 1 mcb nước mắm + 0.5 mcf tiêu + 0.5 mcf đường + 0.5 mcf bột hành. Đổ vào khuôn lót giấy nến, hấp 20 phút. Đánh đều 2 lòng đỏ còn lại, phết lên mặt chả, hấp tiếp 8 phút đến khi mặt vàng ươm.',
                    '**Nướng sườn + làm mỡ hành:** Làm nóng nồi chiên không dầu 200°C. Nướng sườn 10 phút, lật mặt, phết hỗn hợp mật ong + nước mắm (1:1), nướng thêm 8–10 phút đến khi cháy cạnh bóng đẹp. Nướng than: lửa vừa, trở đều, tổng 15–18 phút. Phi hành lá thái 1cm với 2 mcb mỡ nước nóng già đến khi thơm.',
                    '**Pha nước mắm cơm tấm:** Đun sôi 4 mcb nước + 3 mcb đường đến khi đường tan và hơi kẹo. Để nguội bớt, thêm: 2 mcb nước mắm ngon + 1 mcf nước cốt chanh + 1 tép tỏi băm + ớt thái lát. Khuấy đều – nước mắm chuẩn phải sánh nhẹ, ngọt trội, chua nhẹ.',
                    '**Trình bày và hoàn thiện:** Xới cơm ra đĩa vun nhẹ. Xếp cạnh nhau: 1 miếng sườn nướng + 1 lát chả trứng dày 1cm thái hình thoi + 30g bì heo trộn thính. Rưới mỡ hành nóng lên toàn bộ đĩa. Chan 2–3 mcb nước mắm đặc lên phần cơm. Ăn kèm dưa leo thái lát, cà chua và đồ chua cà rốt củ cải.',
                ],
            ],

            // ===================== HỦ TIẾU KHÔ SA ĐÉC =====================
            [
                'title'       => 'Hủ Tiếu Khô Sa Đéc',
                'description' => 'Món hủ tiếu khô với sợi bánh dai mềm, nước sốt sền sệt đậm đà đặc trưng miền Tây kết hợp tôm, xá xíu thơm lừng.',
                'time_to_cook'=> 45,
                'difficulty'  => 'trung bình',
                'image'       => 'https://images.unsplash.com/photo-1618040996337-56904b7850b9?w=800',
                'category'    => 'Món Khô',
                'tips'        => 'Trộn sợi hủ tiếu với một ít dầu tỏi phi trước khi rưới sốt để hủ tiếu không bị dính và thơm hơn.',
                'ingredients' => [
                    ['name' => 'Hủ tiếu dai Sa Đéc',   'quantity' => '400', 'unit' => 'g'],
                    ['name' => 'Tôm sú tươi',         'quantity' => '200', 'unit' => 'g'],
                    ['name' => 'Thịt nạc vai heo',     'quantity' => '300', 'unit' => 'g (làm xá xíu)'],
                    ['name' => 'Gan heo',             'quantity' => '100', 'unit' => 'g'],
                    ['name' => 'Tỏi phi thơm',         'quantity' => '3',   'unit' => 'muỗng canh'],
                    ['name' => 'Hắc xì dầu (sốt)',     'quantity' => '2',   'unit' => 'muỗng canh'],
                    ['name' => 'Dầu hào (sốt)',        'quantity' => '2',   'unit' => 'muỗng canh'],
                    ['name' => 'Đường thốt nốt (sốt)', 'quantity' => '1.5', 'unit' => 'muỗng canh'],
                    ['name' => 'Hẹ và giá đỗ',         'quantity' => '150', 'unit' => 'g'],
                ],
                'steps' => [
                    '**Làm xá xíu:** Thịt vai heo ướp với ngũ vị hương, tỏi băm, dầu hào, mật ong trong 1 tiếng rồi đem nướng chín vàng ở 180°C trong 20 phút. Sau đó thái lát mỏng vừa ăn.',
                    '**Sơ chế tôm và gan:** Tôm rửa sạch luộc chín bóc vỏ. Gan heo luộc chín với chút muối và gừng, thái lát mỏng.',
                    '**Pha nước sốt hủ tiếu khô:** Đun nóng chảo với dầu tỏi phi, thêm hắc xì dầu, dầu hào, đường thốt nốt, chút nước dùng xương. Nấu lửa nhỏ đến khi hỗn hợp sánh sệt, nếm có vị mặn ngọt đậm đà.',
                    '**Trụng hủ tiếu và rau:** Trụng giá, hẹ và sợi hủ tiếu qua nước sôi rồi vớt ngay ra tô. Trộn hủ tiếu với dầu tỏi phi để sợi hủ tiếu tơi, thơm và không dính.',
                    '**Trình bày:** Xếp thịt xá xíu, tôm, gan heo lên mặt tô hủ tiếu. Rưới 2-3 muỗng canh nước sốt đậm đà lên trên, rắc hành lá cắt nhỏ, tỏi phi và ngò rí. Ăn kèm một chén nước dùng xương nóng hổi kế bên.',
                ],
            ],

            // ===================== GORDON RAMSAY =====================
            [
                'title'        => 'Bò Wellington Thượng Hạng',
                'description'  => 'Món bò Wellington độc quyền trứ danh của siêu đầu bếp Gordon Ramsay, với lớp vỏ bánh ngàn lớp giòn rụm bọc ngoài thăn bò phile chín mềm hồng hào.',
                'time_to_cook' => 90,
                'difficulty'   => 'khó',
                'image'        => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800',
                'category'     => 'Món Khô',
                'tips'         => 'Nên sử dụng thịt thăn bò phile nguyên khối chất lượng cao và quấn màng bọc thực phẩm thật chặt trước khi nướng.',
                'is_premium'   => true,
                'price'        => 150000.00,
                'username'     => 'gordon',
                'ingredients'  => [
                    ['name' => 'Thịt thăn bò phile', 'quantity' => '800', 'unit' => 'g'],
                    ['name' => 'Nấm hương tươi',    'quantity' => '250', 'unit' => 'g'],
                    ['name' => 'Giăm bông Parma',   'quantity' => '150', 'unit' => 'g'],
                    ['name' => 'Bột ngàn lớp',      'quantity' => '1',   'unit' => 'cuộn'],
                    ['name' => 'Mù tạt vàng',       'quantity' => '2',   'unit' => 'muỗng canh'],
                ],
                'steps' => [
                    '**Sơ chế thăn bò:** Chuẩn bị khối thịt thăn bò phile nguyên miếng, lọc sạch gân mỡ thừa. Xoa đều muối và tiêu xay lên các mặt thịt, áp chảo nhanh ở lửa lớn 1-2 phút mỗi mặt để khóa nước bên trong. Lấy thịt ra phết đều mù tạt vàng lên bề mặt khi còn nóng.',
                    '**Làm nhân nấm (Duxelles):** Cho nấm hương tươi đã xay thật nhuyễn vào chảo cùng hành khô băm và tỏi băm. Xào không cần dầu ở lửa vừa cho đến khi nấm chín hoàn toàn và bốc hết hơi nước, hỗn hợp thật khô ráo.',
                    '**Cuộn khối thịt:** Trải màng bọc thực phẩm lên bàn sạch. Xếp chồng nhẹ các lát giăm bông Parma lên nhau thành hình chữ nhật đủ bọc khối thịt. Phết đều hỗn hợp nấm khô lên giăm bông. Đặt khối thịt bò lên và dùng màng bọc quấn chặt lại thành hình trụ tròn. Để tủ lạnh 30 phút cho săn chắc.',
                    '**Bọc bột bánh:** Cán mỏng bột bánh ngàn lớp (puff pastry). Lột màng bọc thực phẩm của cuộn thịt bò ra, đặt khối thịt lên tấm bột bánh và bọc kín lại, cắt bỏ bột thừa. Dùng lòng đỏ trứng gà đánh tan phết lên bề mặt bánh để tạo màu vàng bóng.',
                    '**Nướng bánh:** Làm nóng lò nướng 200 độ C. Đặt cuộn bánh lên khay có lót giấy nến và nướng khoảng 30-35 phút. Lấy ra để nguội bớt khoảng 10 phút trước khi thái lát mỏng 2cm và thưởng thức.',
                ],
            ],

            // ===================== CHRISTINE HÀ =====================
            [
                'title'        => 'Cá Lóc Kho Tộ Christine Hà',
                'description'  => 'Món cá lóc kho tộ đậm vị truyền thống quê hương, công thức đã giúp Christine Hà chinh phục ban giám khảo MasterChef Mỹ.',
                'time_to_cook' => 45,
                'difficulty'   => 'trung bình',
                'image'        => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800',
                'category'     => 'Món Khô',
                'tips'         => 'Kho bằng tộ đất (nồi đất) ở lửa thật nhỏ để cá ngấm sâu gia vị và nước kho kẹo sánh lại.',
                'is_premium'   => true,
                'price'        => 80000.00,
                'username'     => 'christine',
                'ingredients'  => [
                    ['name' => 'Cá lóc tươi',   'quantity' => '500', 'unit' => 'g'],
                    ['name' => 'Thịt ba chỉ',    'quantity' => '150', 'unit' => 'g'],
                    ['name' => 'Nước dừa tươi',  'quantity' => '200', 'unit' => 'ml'],
                    ['name' => 'Nước mắm ngon',  'quantity' => '3',   'unit' => 'muỗng canh'],
                    ['name' => 'Đường thốt nốt', 'quantity' => '2',   'unit' => 'muỗng canh'],
                ],
                'steps' => [
                    '**Sơ chế nguyên liệu:** Cá lóc làm sạch ruột và vảy, xát muối chanh khử tanh rồi rửa sạch, cắt khoanh dày 2cm. Thịt ba chỉ thái miếng nhỏ dài bằng ngón tay.',
                    '**Ướp cá:** Ướp cá lóc với hành băm, tỏi băm, tiêu xay, 2 muỗng nước mắm ngon và chút đường trong 30 phút cho cá cứng và ngấm vị.',
                    '**Thắng nước màu:** Đặt tộ đất lên bếp, cho 2 muỗng đường thốt nốt và một chút dầu ăn vào đun nhỏ lửa, khuấy đều tay đến khi đường tan và chuyển sang màu vàng cánh gián sậm.',
                    '**Xào thịt ba chỉ:** Cho thịt ba chỉ vào tộ đất, đảo đều cho thịt săn lại và ra bớt mỡ thơm.',
                    '**Kho cá:** Xếp cá lóc đè lên lớp thịt ba chỉ. Đổ hết phần nước ướp cá và nước màu vào, đun sôi 3 phút. Tiếp tục cho nước dừa tươi vào ngập mặt cá. Đun nhỏ lửa liu riu trong 25-30 phút đến khi nước kho sánh lại. Rắc hành lá cắt khúc và ớt xiêm lên trên trước khi tắt bếp.',
                ],
            ],
            // ===================== KIỆT =====================
            [
                'title'        => 'Sườn Xào Chua Ngọt Premium',
                'description'  => 'Món sườn non xào chua ngọt với nước sốt đặc biệt sánh mịn được tinh chế bởi Kiệt.',
                'time_to_cook' => 40,
                'difficulty'   => 'trung bình',
                'image'        => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800',
                'category'     => 'Món Khô',
                'tips'         => 'Nên rán sườn vàng đều các mặt trước khi sốt để miếng sườn thơm ngon hơn.',
                'is_premium'   => true,
                'price'        => 30000.00,
                'username'     => 'kiet',
                'ingredients'  => [
                    ['name' => 'Sườn non', 'quantity' => '500', 'unit' => 'g'],
                    ['name' => 'Hành tây', 'quantity' => '1', 'unit' => 'củ'],
                    ['name' => 'Ớt chuông', 'quantity' => '1', 'unit' => 'quả'],
                    ['name' => 'Giấm gạo', 'quantity' => '2', 'unit' => 'muỗng canh'],
                    ['name' => 'Đường', 'quantity' => '2', 'unit' => 'muỗng canh'],
                ],
                'steps' => [
                    '**Sơ chế sườn:** Sườn non chặt miếng vừa ăn, luộc sơ qua nước sôi để khử mùi hôi, sau đó rửa lại bằng nước sạch.',
                    '**Rán sườn:** Cho sườn vào chảo rán vàng đều các mặt ở lửa vừa, vớt ra để ráo dầu.',
                    '**Pha nước sốt:** Trộn đều giấm gạo, đường, nước mắm ngon, tương cà và một chút bột năng với nước lọc.',
                    '**Xào rau củ:** Hành tây và ớt chuông cắt miếng vuông vừa ăn, xào sơ qua trên chảo nóng.',
                    '**Hoàn thiện:** Đổ sườn đã rán và nước sốt vào chảo rau củ, đun nhỏ lửa đến khi nước sốt sệt lại bọc đều sườn.'
                ],
            ],

            // ===================== HAHA =====================
            [
                'title'        => 'Lẩu Thái Hải Sản Haha',
                'description'  => 'Món lẩu Thái hải sản chua cay thơm nồng vị sả lá chanh cực kì đậm đà của đầu bếp Haha.',
                'time_to_cook' => 60,
                'difficulty'   => 'trung bình',
                'image'        => 'https://images.unsplash.com/photo-1552611052-33e04de081de?w=800',
                'category'     => 'Món Nước',
                'tips'         => 'Sử dụng nước cốt dừa và sữa đặc để tạo vị béo ngậy chuẩn vị Thái Lan.',
                'is_premium'   => true,
                'price'        => 50000.00,
                'username'     => 'haha',
                'ingredients'  => [
                    ['name' => 'Tôm sú', 'quantity' => '300', 'unit' => 'g'],
                    ['name' => 'Mực tươi', 'quantity' => '300', 'unit' => 'g'],
                    ['name' => 'Sả', 'quantity' => '5', 'unit' => 'nhánh'],
                    ['name' => 'Lá chanh', 'quantity' => '10', 'unit' => 'lá'],
                    ['name' => 'Nấm kim châm', 'quantity' => '200', 'unit' => 'g'],
                ],
                'steps' => [
                    '**Sơ chế hải sản:** Tôm cắt râu, mực làm sạch thái miếng khía hoa, rửa sạch để ráo.',
                    '**Nấu nước dùng:** Ninh xương gà lấy nước dùng. Đập dập sả, cắt khúc, vò lá chanh cho vào nồi nước dùng đun sôi.',
                    '**Nêm nếm gia vị lẩu:** Thêm gói gia vị lẩu Thái, nước mắm, đường, nước cốt chanh, nước cốt dừa và sữa đặc.',
                    '**Trình bày:** Xếp hải sản và nấm ra đĩa lớn. Khi ăn nhúng vào nước lẩu sôi sùng sục.'
                ],
            ],

            // ===================== TESTUSER =====================
            [
                'title'        => 'Bánh Mì Kẹp Thịt Việt Nam',
                'description'  => 'Món bánh mì kẹp thịt Việt Nam giòn rụm với pate béo ngậy và thịt nguội truyền thống của Test User.',
                'time_to_cook' => 20,
                'difficulty'   => 'dễ',
                'image'        => 'https://images.unsplash.com/photo-1621508216335-b2fb3620f4c0?w=800',
                'category'     => 'Món Bánh',
                'tips'         => 'Nên nướng lại bánh mì cho thật giòn trước khi kẹp nhân.',
                'is_premium'   => true,
                'price'        => 25000.00,
                'username'     => 'testuser',
                'ingredients'  => [
                    ['name' => 'Ổ bánh mì', 'quantity' => '1', 'unit' => 'cái'],
                    ['name' => 'Pate gan heo', 'quantity' => '50', 'unit' => 'g'],
                    ['name' => 'Thịt nguội', 'quantity' => '50', 'unit' => 'g'],
                    ['name' => 'Đồ chua', 'quantity' => '30', 'unit' => 'g'],
                    ['name' => 'Hành ngò', 'quantity' => '10', 'unit' => 'g'],
                ],
                'steps' => [
                    '**Sơ chế nhân:** Thái mỏng thịt nguội. Đồ chua để ráo nước. Rửa sạch hành ngò cắt khúc.',
                    '**Chuẩn bị bánh:** Dùng dao xẻ dọc một bên bánh mì, nướng sơ bánh mì trên lò cho nóng giòn.',
                    '**Phết pate:** Phết một lớp pate gan béo ngậy và sốt bơ trứng gà vào ruột bánh mì.',
                    '**Hoàn thiện:** Xếp thịt nguội, đồ chua, hành ngò và chan nước sốt hoặc xịt chút nước tương cùng ớt lát.'
                ],
            ],
        ];

        $defaultUser = User::where('username', 'kiet')->first() ?? $user;

        foreach ($recipes as $data) {
            $title  = $data['title'];
            $authorId = $defaultUser->id;
            if (isset($data['username'])) {
                $specificUser = User::where('username', $data['username'])->first();
                if ($specificUser) {
                    $authorId = $specificUser->id;
                }
            }

            $recipe = Recipe::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title'        => $title,
                    'user_id'      => $authorId,
                    'category_id'  => $categories[$data['category']]->id,
                    'description'  => $data['description'],
                    'time_to_cook' => $data['time_to_cook'],
                    'difficulty'   => $data['difficulty'],
                    'image'        => $data['image'],
                    'views_count'  => rand(100, 5000),
                    'tips'         => $data['tips'],
                    'is_premium'   => $data['is_premium'] ?? false,
                    'price'        => $data['price'] ?? 0.00,
                ]
            );

            // Xóa cũ để tạo mới khi chạy lại
            $recipe->ingredients()->detach();
            $recipe->steps()->delete();

            foreach ($data['ingredients'] as $ing) {
                $ingredientModel = Ingredient::firstOrCreate(['name' => $ing['name']]);
                $recipe->ingredients()->attach($ingredientModel->id, [
                    'quantity' => $ing['quantity'],
                    'unit'     => $ing['unit'],
                ]);
            }

            foreach ($data['steps'] as $index => $stepContent) {
                Step::create([
                    'recipe_id' => $recipe->id,
                    'order'     => $index + 1,
                    'content'   => $stepContent,
                ]);
            }
        }
    }
}
