<?php
session_start();
if (!isset($_SESSION['userloggedin'])) {
    header("Location: login.php");
    exit();
}
include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
    <!--Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>طلباتي</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding-top: 130px;
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            min-height: 100vh;
            color: #2D3748;
        }

        .main-container { display: flex; justify-content: center; margin-bottom: 50px; }
        .container-div { width: 90%; max-width: 1000px; }

        .tabs {
            display: flex; gap: 10px; cursor: pointer; justify-content: center; flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6); padding: 15px; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 30px;
        }

        .tab {
            padding: 10px 25px; border-radius: 15px; transition: all 0.3s;
            font-size: 1.1rem; font-weight: 700; color: #4A5568; background: transparent;
        }
        .tab:hover { background: rgba(255, 255, 255, 0.9); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        .tab.active {
            background: linear-gradient(135deg, #FF7B54, #FFB26B);
            color: white; box-shadow: 0 10px 20px rgba(255, 123, 84, 0.3);
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .order {
            background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.6); box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 30px; margin-bottom: 25px; border-radius: 25px; transition: transform 0.3s;
        }
        .order:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }

        .order-header {
            display: flex; justify-content: space-between; margin-bottom: 20px;
            border-bottom: 2px dashed rgba(0,0,0,0.1); padding-bottom: 15px; align-items: center;
        }

        .order-header div { font-weight: 800; font-size: 1.3rem; color: #2D3748; }

        .order-details, .order-items { margin-bottom: 20px; }

        .order-items { border-top: 2px dashed rgba(0,0,0,0.1); padding-top: 15px; }

        .order-item {
            display: flex; justify-content: space-between; padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 1.1rem; color: #4A5568; font-weight: 600;
        }
        .order-item:last-child { border-bottom: none; }

        .order-total { text-align: left; font-weight: 900; margin-top: 15px; font-size: 1.4rem; color: #FF7B54; }

        .cancel-btn, .review-btn {
            padding: 10px 20px; border: none; border-radius: 12px; cursor: pointer;
            font-weight: 800; font-size: 1.1rem; transition: 0.3s; width: 100%; margin-top: 15px;
        }
        
        .cancel-btn { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .cancel-btn:hover { background: #ef4444; color: white; box-shadow: 0 8px 15px rgba(239, 68, 68, 0.3); }

        .review-btn { background: linear-gradient(135deg, #48BB78, #38A169); color: white; box-shadow: 0 8px 15px rgba(72, 187, 120, 0.3); }
        .review-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(72, 187, 120, 0.4); }

        .customer-details { display: flex; gap: 10px; font-size: 1.1rem; color: #4A5568; margin-bottom: 8px; }
        .customer-details strong { font-weight: 800; color: #2D3748; width: 140px; }

        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 1rem; font-weight: 800; }
        .status-pending .status-text { color: #f97316; background: #ffedd5; padding: 5px 15px; border-radius: 20px; }
        .status-processing .status-text { color: #3b82f6; background: #dbeafe; padding: 5px 15px; border-radius: 20px; }
        .status-on-the-way .status-text { color: #8b5cf6; background: #ede9fe; padding: 5px 15px; border-radius: 20px; }
        .status-completed .status-text { color: #10b981; background: #d1fae5; padding: 5px 15px; border-radius: 20px; }
        .status-cancelled .status-text { color: #ef4444; background: #fee2e2; padding: 5px 15px; border-radius: 20px; }

        /* Modal Background */
        .modal {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(5px); z-index: 10000;
        }

        /* Modal Content */
        .modal-content {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(25px); border: 1px solid rgba(255,255,255,0.5);
            margin: 10% auto; padding: 30px; width: 90%; max-width: 500px; border-radius: 25px; position: relative;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: zoomIn 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        @keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .modal-content h2 { font-weight: 900; color: #2D3748; margin-bottom: 20px; }

        .modal-close { position: absolute; color: #A0AEC0; font-size: 30px; font-weight: bold; top: 15px; right: 20px; cursor: pointer; transition: 0.3s; }
        .modal-close:hover { color: #E53E3E; }

        textarea {
            width: 100%; height: 120px; margin-bottom: 20px !important; padding: 15px; border-radius: 15px;
            border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.9); font-size: 1.1rem;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.02); resize: none; color: #2D3748; font-family: 'Cairo', sans-serif;
        }
        textarea:focus { outline: none; border-color: #FF7B54; box-shadow: 0 0 0 3px rgba(255, 123, 84, 0.2); }

        .modal-content button {
            background: linear-gradient(135deg, #EF4444, #DC2626); color: white; padding: 12px 25px;
            border: none; cursor: pointer; border-radius: 15px; font-weight: 800; font-size: 1.1rem; width: 100%;
            transition: 0.3s; box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }
        .modal-content button:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4); }
        .modal-content button[id="submitReviewBtn"] { background: linear-gradient(135deg, #48BB78, #38A169); box-shadow: 0 5px 15px rgba(72, 187, 120, 0.3); }
        .modal-content button[id="submitReviewBtn"]:hover { box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4); }

        .star-rating { direction: rtl; display: inline-flex; font-size: 2.5rem; unicode-bidi: bidi-override; justify-content: center; width: 100%; margin-bottom: 10px; }
        .star-rating input[type="radio"] { display: none; }
        .star-rating label { color: #E2E8F0; cursor: pointer; transition: 0.2s; padding: 0 5px; }
        .star-rating label:hover, .star-rating label:hover ~ label { color: #F59E0B; transform: scale(1.1); }
        .star-rating input[type="radio"]:checked ~ label { color: #F59E0B; }

        .review-section { background: rgba(0,0,0,0.02); padding: 15px; border-radius: 15px; border: 1px dashed rgba(0,0,0,0.1); margin-top: 15px; }
        .review-section strong { font-weight: 800; color: #2D3748; }

        .review { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 10px; }
        .review p { margin: 0; color: #4A5568; }

        @media screen and (max-width: 900px) {
            .tabs { padding: 10px; }
            .tab { padding: 8px 15px; font-size: 1rem; }
            .container-div { width: 95%; }
            .order { padding: 20px; }
        }
    </style>
</head>

<body>
    <?php
    include_once("nav-logged.php");
    ?>
    <div class="main-container" dir="rtl" style="text-align: right;">
        <div class="container-div">
            <div class="tabs">
                <div class="tab active" data-status="All">الكل</div>
                <div class="tab" data-status="Pending">قيد الانتظار</div>
                <div class="tab" data-status="Processing">يعالج</div>
                <div class="tab" data-status="On the way">في طريق</div>
                <div class="tab" data-status="Completed">مكتمل</div>
                <div class="tab" data-status="Cancelled">تم الإلغاء</div>
            </div>
            <div id="orders">
                <div class="tab-content active" id="all-orders"></div>
                <div class="tab-content" id="pending-orders"></div>
                <div class="tab-content" id="processing-orders"></div>
                <div class="tab-content" id="on-the-way-orders"></div>
                <div class="tab-content" id="completed-orders"></div>
                <div class="tab-content" id="cancelled-orders"></div>
            </div>
        </div>
    </div>


    <!-- Cancel Reason Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>إلغاء الطلب</h2>
            <textarea id="cancelReason" placeholder="أدخل سبب الإلغاء..."></textarea>
            <button id="cancelOrderBtn">إلغاء الطلب</button>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content" dir="rtl" style="text-align: right;">
            <span class="modal-close">&times;</span>
            <h2>أرسل تقييمك</h2>
            <form id="reviewForm" action="submit_reviews.php" method="POST">

                <input type="hidden" name="email" value="<?php echo $userEmail; ?>"> <!-- Hidden email field -->
                <input type="hidden" id="reviewOrderId" name="orderId">
                <!-- Display Stars -->
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" />
                    <label for="star5" title="5 stars">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="4 stars">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="3 stars">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="2 stars">&#9733;</label>
                    <input type="radio" id="star1" name="rating" value="1" />
                    <label for="star1" title="1 star">&#9733;</label>
                </div>
                <br>
                <label for="reviewText">مراجعة:</label>
                <textarea id="reviewText" name="reviewText" rows="4" cols="50"></textarea>
                <br>

                <br>
                <button type="submit" id="submitReviewBtn" class="review-btn">أرسل تقييمك</button>
            </form>
        </div>
    </div>
   
    <?php
include_once ('footer.html');
?>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('Page is ready. Calling load_cart_item_number.');
            load_cart_item_number();

            function load_cart_item_number() {
                $.ajax({
                    url: 'action.php',
                    method: 'get',
                    data: {
                        cartItem: "cart_item"
                    },
                    success: function(response) {
                        $("#cart-item").html(response);
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-rating input[type="radio"]');

            stars.forEach(star => {
                star.addEventListener('change', function() {
                    const rating = this.value;
                    console.log('Selected rating:', rating);
                    // You can send this rating value to your server via AJAX or form submission
                });
            });
        });
    </script>
    <script>
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelector('.tab.active').classList.remove('active');
                this.classList.add('active');

                const status = this.getAttribute('data-status');
                document.querySelector('.tab-content.active').classList.remove('active');
                document.getElementById(`${status.toLowerCase().replace(/ /g, '-')}-orders`).classList.add('active');

                fetchOrders(status);
            });
        });

        function fetchOrders(status) {
            fetch(`fetch_orders.php?status=${status}`)
                .then(response => response.json())
                .then(data => {
                    data.sort((a, b) => new Date(b.order_date) - new Date(a.order_date)); // Sort orders in descending order based on order_date

                    const ordersContainer = document.getElementById(`${status.toLowerCase().replace(/ /g, '-')}-orders`);
                    ordersContainer.innerHTML = data.map(order => `
                        <div class="order">
                            <div class="order-header">
                                <div><i class="fas fa-hashtag" style="color:#FF7B54;"></i> رقم الطلب: ${order.order_id}</div>
                                <div class="status ${getStatusClass(order.order_status)}"><span class="status-text">${order.order_status}</span></div>
                            </div>
                            
                            ${(order.order_status !== 'Cancelled' && order.order_status !== 'تم الإلغاء') ? `
                            <div class="tracking-progress" style="margin: 15px 0 35px 0;">
                                <div style="display: flex; justify-content: space-between; position: relative;">
                                    <div style="position: absolute; top: 20px; left: 5%; right: 5%; height: 6px; background: rgba(0,0,0,0.05); border-radius: 3px; z-index: 1;"></div>
                                    <div style="position: absolute; top: 20px; right: 5%; height: 6px; background: linear-gradient(90deg, #48BB78, #38A169); border-radius: 3px; z-index: 2; transition: width 1s ease; width: ${getTrackingProgress(order.order_status)}%;"></div>
                                    
                                    <div style="text-align: center; z-index: 3; flex: 1; position: relative;">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: ${['قيد الانتظار', 'Pending', 'يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? 'linear-gradient(135deg, #48BB78, #38A169)' : 'white'}; border: 2px solid ${['قيد الانتظار', 'Pending', 'يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#E2E8F0'}; color: ${['قيد الانتظار', 'Pending', 'يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? 'white' : '#A0AEC0'}; line-height: 41px; margin: 0 auto 10px auto; font-size: 1.2rem; box-shadow: 0 5px 10px rgba(0,0,0,0.05); transition: 0.5s;"><i class="fas fa-clipboard-list"></i></div>
                                        <div style="font-size: 0.9rem; font-weight: 800; color: ${['قيد الانتظار', 'Pending', 'يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#A0AEC0'};">تأكيد</div>
                                    </div>
                                    <div style="text-align: center; z-index: 3; flex: 1; position: relative;">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: ${['يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? 'linear-gradient(135deg, #48BB78, #38A169)' : 'white'}; border: 2px solid ${['يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#E2E8F0'}; color: ${['يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? 'white' : '#A0AEC0'}; line-height: 41px; margin: 0 auto 10px auto; font-size: 1.2rem; box-shadow: 0 5px 10px rgba(0,0,0,0.05); transition: 0.5s;"><i class="fas fa-box"></i></div>
                                        <div style="font-size: 0.9rem; font-weight: 800; color: ${['يعالج', 'Processing', 'في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#A0AEC0'};">تجهيز</div>
                                    </div>
                                    <div style="text-align: center; z-index: 3; flex: 1; position: relative;">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: ${['في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? 'linear-gradient(135deg, #48BB78, #38A169)' : 'white'}; border: 2px solid ${['في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#E2E8F0'}; color: ${['في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? 'white' : '#A0AEC0'}; line-height: 41px; margin: 0 auto 10px auto; font-size: 1.2rem; box-shadow: 0 5px 10px rgba(0,0,0,0.05); transition: 0.5s;"><i class="fas fa-truck"></i></div>
                                        <div style="font-size: 0.9rem; font-weight: 800; color: ${['في الطريق', 'On the way', 'مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#A0AEC0'};">في الطريق</div>
                                    </div>
                                    <div style="text-align: center; z-index: 3; flex: 1; position: relative;">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: ${['مكتمل', 'Completed'].includes(order.order_status) ? 'linear-gradient(135deg, #48BB78, #38A169)' : 'white'}; border: 2px solid ${['مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#E2E8F0'}; color: ${['مكتمل', 'Completed'].includes(order.order_status) ? 'white' : '#A0AEC0'}; line-height: 41px; margin: 0 auto 10px auto; font-size: 1.2rem; box-shadow: 0 5px 10px rgba(0,0,0,0.05); transition: 0.5s;"><i class="fas fa-check"></i></div>
                                        <div style="font-size: 0.9rem; font-weight: 800; color: ${['مكتمل', 'Completed'].includes(order.order_status) ? '#38A169' : '#A0AEC0'};">تم التوصيل</div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}

                            ${order.delivery_fname ? `
                            <div class="order-details" style="background: rgba(255,123,84,0.05); padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px dashed rgba(255,123,84,0.5);">
                                <div style="color: #FF7B54; margin-bottom: 10px; font-size: 1.2rem; font-weight: 800;"><i class="fas fa-motorcycle"></i> بيانات مندوب التوصيل</div>
                                <div class="customer-details">
                                    <strong>الإسم:</strong> <span>${order.delivery_fname} ${order.delivery_lname || ''}</span>
                                </div>
                                <div class="customer-details">
                                    <strong>رقم الهاتف:</strong> <span>${order.delivery_contact || 'غير متوفر'}</span>
                                </div>
                            </div>
                            ` : ''}

                            <div class="order-details">
                                <div class="customer-details">
                                    <strong>الإسم:</strong> <span>${order.firstName} ${order.lastName}</span>
                                </div>
                                <div class="customer-details">
                                    <strong>العنوان:</strong> <span>${order.address}</span>
                                </div>
                                <div class="customer-details">
                                    <strong>رقم الهاتف:</strong> <span>${order.phone}</span>
                                </div>
                                <div class="customer-details">
                                    <strong>طريقة الدفع:</strong> <span>${order.pmode}</span>
                                </div>
                                <div class="customer-details">
                                    <strong>تاريخ الطلب:</strong> <span dir="ltr">${new Date(order.order_date).toLocaleString()}</span>
                                </div>
                                <div class="customer-details">
                                    <strong>ملاحظة:</strong> <span>${order.note || 'لا توجد'}</span>
                                </div>
                            </div>
                            <div class="order-items">
                                ${order.items.map(item => `
                                    <div class="order-item">
                                        <div><span style="color:#FF7B54;">${item.quantity}x</span> ${item.itemName}</div>
                                        <div>YER ${item.total_price}</div>
                                    </div>
                                `).join('')}
                                 <div class="order-total">الإجمالي: YER&nbsp;${order.grand_total}</div>
                        ${(order.order_status === 'Cancelled' || order.order_status === 'تم الإلغاء') ? `
                        <div class="review-section">
                        <div class="review"><p><strong>سبب الإلغاء: </strong> ${order.cancel_reason}</p></div>
                        </div>` : ''}
                    </div>
                   ${(order.order_status !== 'Completed' && order.order_status !== 'Cancelled' && order.order_status !== 'مكتمل' && order.order_status !== 'تم الإلغاء') ? `<button class="cancel-btn" onclick="openCancelModal(${order.order_id})">إلغاء الطلب</button>` : ''}
                    ${((order.order_status === 'Completed' || order.order_status === 'مكتمل') || (order.order_status === 'Cancelled' || order.order_status === 'تم الإلغاء')) && !order.review_text ? `
                        <button class="review-btn" onclick="openReviewModal(${order.order_id})">اكتب مراجعة <i class="fas fa-star" style="margin-right:5px;"></i></button>
                    ` : ''}
                    ${((order.order_status === 'Completed' || order.order_status === 'مكتمل') || (order.order_status === 'Cancelled' || order.order_status === 'تم الإلغاء')) && order.review_text ? `
                        <div class="review-section">
                         <div class="review">
                            <p><strong>تقييمك: </strong> <span>${order.review_text}</span></p>
                         </div>
                            ${order.response ? `
                            <div class="review">
                              <p><strong>إجابة: </strong> <span>${order.response}</span></p>
                            </div>` : ''}
                        </div>
                    ` : ''}
                </div>
            `).join('');
                })
                .catch(error => console.error('خطأ في جلب الطلبات:', error));
        }

        function getStatusClass(status) {
            switch (status) {
                case 'قيد الانتظار':
                    return 'status-pending';
                case 'يعالج':
                    return 'status-processing';
                case 'في طريق':
                    return 'status-on-the-way';
                case 'مكتمل':
                    return 'status-completed';
                case 'تم الإلغاء':
                    return 'status-cancelled';
                default:
                    return '';
            }
        }

        function getTrackingProgress(status) {
            switch (status) {
                case 'قيد الانتظار':
                case 'Pending':
                    return 10;
                case 'يعالج':
                case 'Processing':
                    return 45;
                case 'في طريق':
                case 'في الطريق':
                case 'On the way':
                    return 80;
                case 'مكتمل':
                case 'Completed':
                    return 100;
                default:
                    return 0;
            }
        }




        // Load all orders by default
        fetchOrders('All');
    </script>

    <script>
        // Function to open Cancel Modal
        function openCancelModal(orderId) {
            document.getElementById("cancelModal").setAttribute("data-order-id", orderId);
            document.getElementById("cancelModal").style.display = "block";
        }



        // Close Cancel Modal
        document.querySelector(".modal-close").onclick = function() {
            document.getElementById("cancelModal").style.display = "none";
        };

        // Handle Cancel Order button click
        document.getElementById("cancelOrderBtn").onclick = function() {
            var cancelReason = document.getElementById("cancelReason").value;
            var orderId = document.getElementById("cancelModal").getAttribute("data-order-id");

            if (cancelReason.trim() === "") {
                alert("يرجى إدخال سبب الإلغاء.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "cancel_order.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("تم إلغاء الطلب.");
                    window.location.href = "orders.php";
                } else {
                    alert("فشل إلغاء الطلب. يرجى المحاولة مرة أخرى.");
                }
            };
            xhr.onerror = function() {
                console.error("فشل الطلب.");
            };
            xhr.send("orderId=" + encodeURIComponent(orderId) + "&reason=" + encodeURIComponent(cancelReason));

            document.getElementById("cancelModal").style.display = "none";
        };

        // Open Review Modal
        function openReviewModal(orderId) {
            document.getElementById("reviewOrderId").value = orderId; // Set the hidden order ID field
            document.getElementById("reviewModal").style.display = "block"; // Show the modal
        }

        // Close Review Modal
        function closeReviewModal() {
            document.getElementById("reviewModal").style.display = "none"; // Hide the modal
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById("reviewModal");
            if (event.target === modal) { // Check if the click is outside the modal content
                closeReviewModal();
            }
        };

        // Optional: Close modal when clicking on the close button inside the modal
        document.querySelector(".modal-close").addEventListener("click", closeReviewModal);


        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById("cancelModal")) {
                document.getElementById("cancelModal").style.display = "none";
            } else if (event.target == document.getElementById("reviewModal")) {
                closeReviewModal();
            }
        };

        // Load all orders by default
        fetchOrders('All');
    </script>





</body>

</html>