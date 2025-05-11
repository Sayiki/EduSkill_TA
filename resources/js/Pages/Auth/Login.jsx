import Checkbox from "@/Components/Checkbox";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm, router } from "@inertiajs/react";
import axios from "axios";

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();

        // 1) Breeze login
        post(route("login"), {
            onSuccess: () => {
                // 2) then get JWT
                axios
                    .post("/api/login", {
                        email: data.email,
                        password: data.password,
                    })
                    .then((res) => {
                        const token = res.data.access_token;
                        // 3) store it
                        localStorage.setItem("jwt", token);
                        console.log("JWT stored:", localStorage.getItem("jwt"));
                        // 4) set default header
                        axios.defaults.headers.common[
                            "Authorization"
                        ] = `Bearer ${token}`;
                    })
                    .catch((err) => {
                        console.error("API login failed:", err);
                    });
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />
            {status && <div className="mb-4 text-green-600">{status}</div>}

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused
                        onChange={(e) => setData("email", e.target.value)}
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(e) => setData("password", e.target.value)}
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData("remember", e.target.checked)
                            }
                        />
                        <span className="ms-2 text-sm text-gray-600">
                            Remember me
                        </span>
                    </label>
                </div>

                <div className="mt-4 flex justify-end items-center">
                    {canResetPassword && (
                        <Link
                            href={route("password.request")}
                            className="text-sm text-gray-600 underline hover:text-gray-900"
                        >
                            Forgot your password?
                        </Link>
                    )}
                    <PrimaryButton className="ms-4" disabled={processing}>
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
