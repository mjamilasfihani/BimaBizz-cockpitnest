'use client';
import { useState, useEffect } from 'react';

const PaymentButton = ({ product }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [isPaymentClicked, setIsPaymentClicked] = useState(false);
    const [customerDetails, setCustomerDetails] = useState({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        city: '',
        postal_code: '',
        country_code: 'IDN'
    });
    const [quantity, setQuantity] = useState(1);

    useEffect(() => {
        const script = document.createElement('script');
        script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
        script.setAttribute('data-client-key', 'YOUR_CLIENT_KEY');
        script.async = true;
        document.body.appendChild(script);

        return () => {
            document.body.removeChild(script);
        };
    }, []);

    const handlePayment = () => {
        const grossAmount = product.price * quantity;

        if (!customerDetails.first_name || !customerDetails.last_name || !customerDetails.email || !customerDetails.phone || !customerDetails.address || !customerDetails.city || !customerDetails.postal_code) {
            alert('Please fill in all required fields.');
            return;
        }

        setIsPaymentClicked(true);

        const orderData = {
            data: {
                items: [
                    {
                        products: {
                            _model: "products",
                            _id: product._id
                        },
                        jumlah_barang: quantity
                    }
                ],
                totalHarga: grossAmount,
                penerima: {
                    nama: customerDetails.first_name + ' ' + customerDetails.last_name,
                    email: customerDetails.email,
                    noTlpn: customerDetails.phone,
                    alamatTujuan: customerDetails.address + ', ' + customerDetails.city + ', ' + customerDetails.postal_code
                }
            }
        };

        fetch(`${process.env.NEXT_PUBLIC_HOST}/api/content/item/order`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData),
        })
        .then(response => response.json())
        .then(orderResponse => {
            if (orderResponse._id) {
                const transactionData = {
                    data: {
                        transaction_details: {
                            order_id: orderResponse._id,
                            gross_amount: grossAmount,
                        },
                        customer_details: {
                            first_name: customerDetails.first_name,
                            last_name: customerDetails.last_name,
                            email: customerDetails.email,
                            phone: customerDetails.phone,
                        },
                        item_details: [
                            {
                                id: product._id,
                                price: product.price,
                                quantity: quantity,
                                name: product.title
                            }
                        ],
                        shipping_address: {
                            address: customerDetails.address,
                            city: customerDetails.city,
                            postal_code: customerDetails.postal_code,
                            country_code: customerDetails.country_code
                        },
                        payment_type: 'credit_card',
                        credit_card: {
                            secure: true
                        }
                    },
                };

                fetch('http://localhost/cockpit-pro/api/paymentgateway/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(transactionData),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.token) {
                        window.snap.embed(data.token, {
                            embedId: 'snap-container'
                        });
                    } else {
                        alert('Payment failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                alert('Order creation failed: ' + orderResponse.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setCustomerDetails(prevState => ({
            ...prevState,
            [name]: value
        }));
    };

    const handleQuantityChange = (amount) => {
        setQuantity(prevQuantity => Math.max(1, prevQuantity + amount));
    };

    return (
        <>
            <button className="btn btn-primary" onClick={() => setIsOpen(true)}>Beli Sekarang</button>
            {isOpen && (
                <div className="modal modal-open pr-4 -left-4">
                    <div className="modal-box max-w-5xl">
                        <div className="flex">
                            <div className={`w-full ${isPaymentClicked ? '' : 'hidden'}`}>
                                <div id="snap-container" className="w-full h-full rounded-lg"></div>
                            </div>
                            <div className={`w-full p-4 overflow-y-auto ${isPaymentClicked ? 'hidden' : ''}`}>
                                <h3 className="text-2xl font-medium mb-6">Pembelian Barang</h3>
                                <h4 className="text-xl font-bold">{product.title}</h4>
                                <h3 className="text-lg font-medium mt-6">Jumlah Pembelian</h3>
                                <div className="flex items-center justify-between mt-4 space-x-4">
                                    <p className="text-xl my-auto">Rp. {product.price * quantity}</p>
                                    <div className="flex items-center mt-2">
                                        <button className="btn btn-primary" onClick={() => handleQuantityChange(-1)}>-</button>
                                        <span className="mx-6">{quantity}</span>
                                        <button className="btn btn-primary" onClick={() => handleQuantityChange(1)}>+</button>
                                    </div>
                                </div>
                                <h3 className="text-lg font-medium mt-6">Customer Details</h3>
                                <form className="mt-4">
                                    <div className="form-control">
                                        <label className="label">First Name</label>
                                        <input type="text" name="first_name" className="input input-bordered" value={customerDetails.first_name} onChange={handleChange} required />
                                    </div>
                                    <div className="form-control">
                                        <label className="label">Last Name</label>
                                        <input type="text" name="last_name" className="input input-bordered" value={customerDetails.last_name} onChange={handleChange} required />
                                    </div>
                                    <div className="form-control">
                                        <label className="label">Email</label>
                                        <input type="email" name="email" className="input input-bordered" value={customerDetails.email} onChange={handleChange} required />
                                    </div>
                                    <div className="form-control">
                                        <label className="label">Phone</label>
                                        <input type="text" name="phone" className="input input-bordered" value={customerDetails.phone} onChange={handleChange} required />
                                    </div>
                                    <div className="form-control">
                                        <label className="label">Address</label>
                                        <input type="text" name="address" className="input input-bordered" value={customerDetails.address} onChange={handleChange} required />
                                    </div>
                                    <div className="form-control">
                                        <label className="label">City</label>
                                        <input type="text" name="city" className="input input-bordered" value={customerDetails.city} onChange={handleChange} required />
                                    </div>
                                    <div className="form-control">
                                        <label className="label">Postal Code</label>
                                        <input type="text" name="postal_code" className="input input-bordered" value={customerDetails.postal_code} onChange={handleChange} required />
                                    </div>
                                </form>
                                <button className="btn btn-primary mt-4" onClick={handlePayment}>Proceed to Payment</button>
                            </div>
                        </div>
                        <div className="modal-action">
                            <button className="btn" onClick={() => setIsOpen(false)}>Close</button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default PaymentButton;