--
-- PostgreSQL database dump
--

\restrict oaRbZkpuTGbvBhldZkfgCxNEWd2zAIeVpLroj7FRPPnc7aomrFiiMi76VyN1zdU

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE IF EXISTS bizmark_db;
--
-- Name: bizmark_db; Type: DATABASE; Schema: -; Owner: admin
--

CREATE DATABASE bizmark_db WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';


ALTER DATABASE bizmark_db OWNER TO admin;

\unrestrict oaRbZkpuTGbvBhldZkfgCxNEWd2zAIeVpLroj7FRPPnc7aomrFiiMi76VyN1zdU
\connect bizmark_db
\restrict oaRbZkpuTGbvBhldZkfgCxNEWd2zAIeVpLroj7FRPPnc7aomrFiiMi76VyN1zdU

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: ai_processing_logs; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.ai_processing_logs (
    id bigint NOT NULL,
    document_id bigint,
    template_id bigint NOT NULL,
    project_id bigint NOT NULL,
    operation_type character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    input_tokens integer,
    output_tokens integer,
    cost numeric(10,6),
    error_message text,
    metadata json,
    initiated_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT ai_processing_logs_operation_type_check CHECK (((operation_type)::text = ANY ((ARRAY['paraphrase'::character varying, 'summarize'::character varying, 'extract'::character varying, 'analyze'::character varying])::text[]))),
    CONSTRAINT ai_processing_logs_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'processing'::character varying, 'completed'::character varying, 'failed'::character varying])::text[])))
);


ALTER TABLE public.ai_processing_logs OWNER TO admin;

--
-- Name: ai_processing_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.ai_processing_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ai_processing_logs_id_seq OWNER TO admin;

--
-- Name: ai_processing_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.ai_processing_logs_id_seq OWNED BY public.ai_processing_logs.id;


--
-- Name: articles; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.articles (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    excerpt text,
    content text NOT NULL,
    featured_image character varying(255),
    category character varying(255) DEFAULT 'general'::character varying NOT NULL,
    tags json,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    published_at timestamp(0) without time zone,
    views_count integer DEFAULT 0 NOT NULL,
    author_id bigint NOT NULL,
    meta_title character varying(255),
    meta_description text,
    meta_keywords character varying(255),
    is_featured boolean DEFAULT false NOT NULL,
    reading_time integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT articles_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'published'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.articles OWNER TO admin;

--
-- Name: articles_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.articles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.articles_id_seq OWNER TO admin;

--
-- Name: articles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.articles_id_seq OWNED BY public.articles.id;


--
-- Name: bank_reconciliations; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.bank_reconciliations (
    id bigint NOT NULL,
    cash_account_id bigint NOT NULL,
    reconciliation_date date NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL,
    opening_balance_book numeric(15,2) NOT NULL,
    opening_balance_bank numeric(15,2) NOT NULL,
    closing_balance_book numeric(15,2) NOT NULL,
    closing_balance_bank numeric(15,2) NOT NULL,
    total_deposits_in_transit numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    total_outstanding_checks numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    total_bank_charges numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    total_bank_credits numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    adjusted_bank_balance numeric(15,2) NOT NULL,
    adjusted_book_balance numeric(15,2) NOT NULL,
    difference numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    status character varying(255) DEFAULT 'in_progress'::character varying NOT NULL,
    bank_statement_file character varying(255),
    bank_statement_format character varying(50),
    notes text,
    reconciled_by bigint,
    reviewed_by bigint,
    approved_by bigint,
    completed_at timestamp(0) without time zone,
    reviewed_at timestamp(0) without time zone,
    approved_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT bank_reconciliations_status_check CHECK (((status)::text = ANY ((ARRAY['in_progress'::character varying, 'completed'::character varying, 'reviewed'::character varying, 'approved'::character varying])::text[])))
);


ALTER TABLE public.bank_reconciliations OWNER TO admin;

--
-- Name: bank_reconciliations_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.bank_reconciliations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bank_reconciliations_id_seq OWNER TO admin;

--
-- Name: bank_reconciliations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.bank_reconciliations_id_seq OWNED BY public.bank_reconciliations.id;


--
-- Name: bank_statement_entries; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.bank_statement_entries (
    id bigint NOT NULL,
    reconciliation_id bigint NOT NULL,
    transaction_date date NOT NULL,
    description text NOT NULL,
    debit_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    credit_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    running_balance numeric(15,2) NOT NULL,
    reference_number character varying(100),
    is_matched boolean DEFAULT false NOT NULL,
    matched_transaction_type character varying(255),
    matched_transaction_id bigint,
    match_confidence character varying(255),
    match_notes text,
    unmatch_reason character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT bank_statement_entries_match_confidence_check CHECK (((match_confidence)::text = ANY ((ARRAY['exact'::character varying, 'fuzzy'::character varying, 'manual'::character varying])::text[]))),
    CONSTRAINT bank_statement_entries_matched_transaction_type_check CHECK (((matched_transaction_type)::text = ANY ((ARRAY['payment'::character varying, 'expense'::character varying, 'invoice_payment'::character varying])::text[]))),
    CONSTRAINT bank_statement_entries_unmatch_reason_check CHECK (((unmatch_reason)::text = ANY ((ARRAY['missing_in_system'::character varying, 'bank_error'::character varying, 'timing_difference'::character varying, 'needs_investigation'::character varying])::text[])))
);


ALTER TABLE public.bank_statement_entries OWNER TO admin;

--
-- Name: bank_statement_entries_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.bank_statement_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bank_statement_entries_id_seq OWNER TO admin;

--
-- Name: bank_statement_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.bank_statement_entries_id_seq OWNED BY public.bank_statement_entries.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO admin;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO admin;

--
-- Name: cash_accounts; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.cash_accounts (
    id bigint NOT NULL,
    account_name character varying(100) NOT NULL,
    account_type character varying(255) DEFAULT 'bank'::character varying NOT NULL,
    account_number character varying(50),
    bank_name character varying(100),
    account_holder character varying(255),
    current_balance numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    initial_balance numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT cash_accounts_account_type_check CHECK (((account_type)::text = ANY ((ARRAY['bank'::character varying, 'cash'::character varying, 'receivable'::character varying, 'payable'::character varying])::text[])))
);


ALTER TABLE public.cash_accounts OWNER TO admin;

--
-- Name: COLUMN cash_accounts.account_name; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.cash_accounts.account_name IS 'Nama akun (Bank BTN, Cash, dll)';


--
-- Name: COLUMN cash_accounts.account_number; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.cash_accounts.account_number IS 'Nomor rekening';


--
-- Name: COLUMN cash_accounts.bank_name; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.cash_accounts.bank_name IS 'Nama bank';


--
-- Name: COLUMN cash_accounts.account_holder; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.cash_accounts.account_holder IS 'Nama pemilik rekening';


--
-- Name: COLUMN cash_accounts.current_balance; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.cash_accounts.current_balance IS 'Saldo saat ini';


--
-- Name: COLUMN cash_accounts.initial_balance; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.cash_accounts.initial_balance IS 'Saldo awal';


--
-- Name: cash_accounts_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.cash_accounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cash_accounts_id_seq OWNER TO admin;

--
-- Name: cash_accounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.cash_accounts_id_seq OWNED BY public.cash_accounts.id;


--
-- Name: clients; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.clients (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    company_name character varying(255),
    industry character varying(255),
    contact_person character varying(255),
    email character varying(255),
    phone character varying(255),
    mobile character varying(255),
    address text,
    city character varying(255),
    province character varying(255),
    postal_code character varying(255),
    npwp character varying(255),
    tax_name character varying(255),
    tax_address text,
    client_type character varying(255) DEFAULT 'company'::character varying NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    password character varying(255),
    email_verified_at timestamp(0) without time zone,
    remember_token character varying(100),
    CONSTRAINT clients_client_type_check CHECK (((client_type)::text = ANY ((ARRAY['individual'::character varying, 'company'::character varying, 'government'::character varying])::text[]))),
    CONSTRAINT clients_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'inactive'::character varying, 'potential'::character varying])::text[])))
);


ALTER TABLE public.clients OWNER TO admin;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO admin;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.clients_id_seq OWNED BY public.clients.id;


--
-- Name: compliance_checks; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.compliance_checks (
    id bigint NOT NULL,
    draft_id bigint NOT NULL,
    document_type character varying(255) DEFAULT 'UKL-UPL'::character varying NOT NULL,
    overall_score numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    structure_score numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    compliance_score numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    formatting_score numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    completeness_score numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    issues jsonb,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    error_message text,
    total_issues integer DEFAULT 0 NOT NULL,
    critical_issues integer DEFAULT 0 NOT NULL,
    warning_issues integer DEFAULT 0 NOT NULL,
    info_issues integer DEFAULT 0 NOT NULL,
    checked_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.compliance_checks OWNER TO admin;

--
-- Name: compliance_checks_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.compliance_checks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.compliance_checks_id_seq OWNER TO admin;

--
-- Name: compliance_checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.compliance_checks_id_seq OWNED BY public.compliance_checks.id;


--
-- Name: document_drafts; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.document_drafts (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    template_id bigint NOT NULL,
    ai_log_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    sections json,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    created_by bigint NOT NULL,
    approved_at timestamp(0) without time zone,
    approved_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT document_drafts_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'reviewed'::character varying, 'approved'::character varying, 'rejected'::character varying, 'exported'::character varying])::text[])))
);


ALTER TABLE public.document_drafts OWNER TO admin;

--
-- Name: document_drafts_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.document_drafts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.document_drafts_id_seq OWNER TO admin;

--
-- Name: document_drafts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.document_drafts_id_seq OWNED BY public.document_drafts.id;


--
-- Name: document_templates; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.document_templates (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    permit_type character varying(255) NOT NULL,
    description text,
    file_name character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    file_size integer NOT NULL,
    mime_type character varying(50) NOT NULL,
    page_count smallint,
    required_fields json,
    is_active boolean DEFAULT true NOT NULL,
    created_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT document_templates_permit_type_check CHECK (((permit_type)::text = ANY ((ARRAY['pertek_bpn'::character varying, 'ukl_upl'::character varying, 'amdal'::character varying, 'imb'::character varying, 'pbg'::character varying, 'slf'::character varying, 'siup'::character varying, 'tdp'::character varying, 'npwp'::character varying, 'other'::character varying])::text[])))
);


ALTER TABLE public.document_templates OWNER TO admin;

--
-- Name: document_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.document_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.document_templates_id_seq OWNER TO admin;

--
-- Name: document_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.document_templates_id_seq OWNED BY public.document_templates.id;


--
-- Name: documents; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.documents (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    task_id bigint,
    title character varying(255) NOT NULL,
    description text,
    category character varying(255) NOT NULL,
    document_type character varying(255),
    file_name character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    file_size character varying(255),
    mime_type character varying(255),
    version character varying(10) DEFAULT '1.0'::character varying NOT NULL,
    parent_document_id bigint,
    is_latest_version boolean DEFAULT true NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    document_date date,
    submission_date date,
    approval_date date,
    uploaded_by bigint NOT NULL,
    is_confidential boolean DEFAULT false NOT NULL,
    access_permissions json,
    notes text,
    download_count integer DEFAULT 0 NOT NULL,
    last_accessed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT documents_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'review'::character varying, 'approved'::character varying, 'submitted'::character varying, 'final'::character varying])::text[])))
);


ALTER TABLE public.documents OWNER TO admin;

--
-- Name: COLUMN documents.category; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.documents.category IS 'proposal, kontrak, kajian, surat, sk, dll';


--
-- Name: COLUMN documents.document_type; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.documents.document_type IS 'PDF, DOC, XLS, etc';


--
-- Name: documents_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.documents_id_seq OWNER TO admin;

--
-- Name: documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.documents_id_seq OWNED BY public.documents.id;


--
-- Name: email_campaigns; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.email_campaigns (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    template_id bigint,
    content text NOT NULL,
    plain_content text,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    recipient_type character varying(255) DEFAULT 'all'::character varying NOT NULL,
    recipient_tags json,
    scheduled_at timestamp(0) without time zone,
    sent_at timestamp(0) without time zone,
    total_recipients integer DEFAULT 0 NOT NULL,
    sent_count integer DEFAULT 0 NOT NULL,
    opened_count integer DEFAULT 0 NOT NULL,
    clicked_count integer DEFAULT 0 NOT NULL,
    bounced_count integer DEFAULT 0 NOT NULL,
    unsubscribed_count integer DEFAULT 0 NOT NULL,
    created_by bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT email_campaigns_recipient_type_check CHECK (((recipient_type)::text = ANY ((ARRAY['all'::character varying, 'active'::character varying, 'tags'::character varying])::text[]))),
    CONSTRAINT email_campaigns_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'scheduled'::character varying, 'sending'::character varying, 'sent'::character varying, 'paused'::character varying, 'cancelled'::character varying])::text[])))
);


ALTER TABLE public.email_campaigns OWNER TO admin;

--
-- Name: email_campaigns_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.email_campaigns_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.email_campaigns_id_seq OWNER TO admin;

--
-- Name: email_campaigns_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.email_campaigns_id_seq OWNED BY public.email_campaigns.id;


--
-- Name: email_inbox; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.email_inbox (
    id bigint NOT NULL,
    message_id character varying(255) NOT NULL,
    from_email character varying(255) NOT NULL,
    from_name character varying(255),
    to_email character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    body_html text,
    body_text text,
    attachments json,
    is_read boolean DEFAULT false NOT NULL,
    is_starred boolean DEFAULT false NOT NULL,
    category character varying(255) DEFAULT 'inbox'::character varying NOT NULL,
    labels json,
    replied_to bigint,
    assigned_to bigint,
    received_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT email_inbox_category_check CHECK (((category)::text = ANY ((ARRAY['inbox'::character varying, 'sent'::character varying, 'trash'::character varying, 'spam'::character varying])::text[])))
);


ALTER TABLE public.email_inbox OWNER TO admin;

--
-- Name: email_inbox_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.email_inbox_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.email_inbox_id_seq OWNER TO admin;

--
-- Name: email_inbox_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.email_inbox_id_seq OWNED BY public.email_inbox.id;


--
-- Name: email_logs; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.email_logs (
    id bigint NOT NULL,
    campaign_id bigint,
    subscriber_id bigint,
    recipient_email character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'sent'::character varying NOT NULL,
    sent_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    opened_at timestamp(0) without time zone,
    clicked_at timestamp(0) without time zone,
    bounced_at timestamp(0) without time zone,
    tracking_id character varying(255),
    error_message character varying(255),
    ip_address character varying(255),
    user_agent character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT email_logs_status_check CHECK (((status)::text = ANY ((ARRAY['sent'::character varying, 'delivered'::character varying, 'opened'::character varying, 'clicked'::character varying, 'bounced'::character varying, 'failed'::character varying])::text[])))
);


ALTER TABLE public.email_logs OWNER TO admin;

--
-- Name: email_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.email_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.email_logs_id_seq OWNER TO admin;

--
-- Name: email_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.email_logs_id_seq OWNED BY public.email_logs.id;


--
-- Name: email_subscribers; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.email_subscribers (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    name character varying(255),
    phone character varying(255),
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    source character varying(255),
    tags json,
    custom_fields json,
    subscribed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    unsubscribed_at timestamp(0) without time zone,
    unsubscribe_reason character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT email_subscribers_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'unsubscribed'::character varying, 'bounced'::character varying])::text[])))
);


ALTER TABLE public.email_subscribers OWNER TO admin;

--
-- Name: email_subscribers_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.email_subscribers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.email_subscribers_id_seq OWNER TO admin;

--
-- Name: email_subscribers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.email_subscribers_id_seq OWNED BY public.email_subscribers.id;


--
-- Name: email_templates; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.email_templates (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    content text NOT NULL,
    plain_content text,
    thumbnail character varying(255),
    category character varying(255) DEFAULT 'newsletter'::character varying NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    variables json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT email_templates_category_check CHECK (((category)::text = ANY ((ARRAY['newsletter'::character varying, 'promotional'::character varying, 'transactional'::character varying, 'announcement'::character varying])::text[])))
);


ALTER TABLE public.email_templates OWNER TO admin;

--
-- Name: email_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.email_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.email_templates_id_seq OWNER TO admin;

--
-- Name: email_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.email_templates_id_seq OWNED BY public.email_templates.id;


--
-- Name: expense_categories; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.expense_categories (
    id bigint NOT NULL,
    slug character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    "group" character varying(255),
    icon character varying(255),
    is_default boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.expense_categories OWNER TO admin;

--
-- Name: expense_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.expense_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.expense_categories_id_seq OWNER TO admin;

--
-- Name: expense_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.expense_categories_id_seq OWNED BY public.expense_categories.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO admin;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO admin;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: institutions; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.institutions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    address text,
    phone character varying(255),
    email character varying(255),
    contact_person character varying(255),
    contact_position character varying(255),
    notes text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.institutions OWNER TO admin;

--
-- Name: COLUMN institutions.type; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.institutions.type IS 'DLH, BPN, OSS, Notaris, dll';


--
-- Name: institutions_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.institutions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.institutions_id_seq OWNER TO admin;

--
-- Name: institutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.institutions_id_seq OWNED BY public.institutions.id;


--
-- Name: invoice_items; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.invoice_items (
    id bigint NOT NULL,
    invoice_id bigint NOT NULL,
    description character varying(255) NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    unit_price numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    "order" integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.invoice_items OWNER TO admin;

--
-- Name: invoice_items_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.invoice_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.invoice_items_id_seq OWNER TO admin;

--
-- Name: invoice_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.invoice_items_id_seq OWNED BY public.invoice_items.id;


--
-- Name: invoices; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.invoices (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    invoice_number character varying(255) NOT NULL,
    invoice_date date NOT NULL,
    due_date date NOT NULL,
    subtotal numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    tax_rate numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    tax_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    total_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    paid_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    remaining_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    notes text,
    client_name character varying(255),
    client_address text,
    client_tax_id character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT invoices_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'sent'::character varying, 'partial'::character varying, 'paid'::character varying, 'overdue'::character varying, 'cancelled'::character varying])::text[])))
);


ALTER TABLE public.invoices OWNER TO admin;

--
-- Name: invoices_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.invoices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.invoices_id_seq OWNER TO admin;

--
-- Name: invoices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.invoices_id_seq OWNED BY public.invoices.id;


--
-- Name: job_applications; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.job_applications (
    id bigint NOT NULL,
    job_vacancy_id bigint NOT NULL,
    full_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    phone character varying(255) NOT NULL,
    birth_date date,
    gender character varying(255),
    address text,
    education_level character varying(255) NOT NULL,
    major character varying(255) NOT NULL,
    institution character varying(255) NOT NULL,
    graduation_year integer,
    gpa numeric(3,2),
    work_experience text,
    has_experience_ukl_upl boolean DEFAULT false NOT NULL,
    skills text,
    cv_path character varying(255),
    portfolio_path character varying(255),
    cover_letter text,
    expected_salary integer,
    available_from date,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    notes text,
    reviewed_at timestamp(0) without time zone,
    reviewed_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT job_applications_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'reviewed'::character varying, 'interview'::character varying, 'accepted'::character varying, 'rejected'::character varying])::text[])))
);


ALTER TABLE public.job_applications OWNER TO admin;

--
-- Name: job_applications_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.job_applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.job_applications_id_seq OWNER TO admin;

--
-- Name: job_applications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.job_applications_id_seq OWNED BY public.job_applications.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO admin;

--
-- Name: job_vacancies; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.job_vacancies (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    "position" character varying(255) NOT NULL,
    description text NOT NULL,
    responsibilities text NOT NULL,
    qualifications text NOT NULL,
    benefits text,
    employment_type character varying(255) DEFAULT 'full-time'::character varying NOT NULL,
    location character varying(255) DEFAULT 'Jakarta'::character varying NOT NULL,
    salary_min integer,
    salary_max integer,
    salary_negotiable boolean DEFAULT true NOT NULL,
    deadline date,
    status character varying(255) DEFAULT 'open'::character varying NOT NULL,
    google_form_url character varying(255),
    applications_count integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT job_vacancies_status_check CHECK (((status)::text = ANY ((ARRAY['open'::character varying, 'closed'::character varying, 'draft'::character varying])::text[])))
);


ALTER TABLE public.job_vacancies OWNER TO admin;

--
-- Name: job_vacancies_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.job_vacancies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.job_vacancies_id_seq OWNER TO admin;

--
-- Name: job_vacancies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.job_vacancies_id_seq OWNED BY public.job_vacancies.id;


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO admin;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO admin;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO admin;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO admin;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: milestones; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.milestones (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.milestones OWNER TO admin;

--
-- Name: milestones_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.milestones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.milestones_id_seq OWNER TO admin;

--
-- Name: milestones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.milestones_id_seq OWNED BY public.milestones.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO admin;

--
-- Name: payment_methods; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.payment_methods (
    id bigint NOT NULL,
    code character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    requires_cash_account boolean DEFAULT false NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payment_methods OWNER TO admin;

--
-- Name: payment_methods_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.payment_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payment_methods_id_seq OWNER TO admin;

--
-- Name: payment_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.payment_methods_id_seq OWNED BY public.payment_methods.id;


--
-- Name: payment_schedules; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.payment_schedules (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    invoice_id bigint,
    description character varying(255) NOT NULL,
    amount numeric(15,2) NOT NULL,
    due_date date NOT NULL,
    paid_date date,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    payment_method character varying(255),
    reference_number character varying(255),
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_reconciled boolean DEFAULT false NOT NULL,
    reconciled_at timestamp(0) without time zone,
    reconciliation_id bigint,
    CONSTRAINT payment_schedules_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'paid'::character varying, 'overdue'::character varying, 'cancelled'::character varying])::text[])))
);


ALTER TABLE public.payment_schedules OWNER TO admin;

--
-- Name: payment_schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.payment_schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payment_schedules_id_seq OWNER TO admin;

--
-- Name: payment_schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.payment_schedules_id_seq OWNED BY public.payment_schedules.id;


--
-- Name: permission_role; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permission_role (
    id bigint NOT NULL,
    role_id bigint NOT NULL,
    permission_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permission_role OWNER TO admin;

--
-- Name: permission_role_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permission_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permission_role_id_seq OWNER TO admin;

--
-- Name: permission_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permission_role_id_seq OWNED BY public.permission_role.id;


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    display_name character varying(255) NOT NULL,
    "group" character varying(255) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO admin;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permissions_id_seq OWNER TO admin;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: permit_documents; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permit_documents (
    id bigint NOT NULL,
    project_permit_id bigint NOT NULL,
    filename character varying(255) NOT NULL,
    original_filename character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    file_type character varying(50) NOT NULL,
    file_size integer NOT NULL,
    description text,
    uploaded_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permit_documents OWNER TO admin;

--
-- Name: permit_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permit_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permit_documents_id_seq OWNER TO admin;

--
-- Name: permit_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permit_documents_id_seq OWNED BY public.permit_documents.id;


--
-- Name: permit_template_dependencies; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permit_template_dependencies (
    id bigint NOT NULL,
    template_id bigint NOT NULL,
    permit_item_id bigint NOT NULL,
    depends_on_item_id bigint NOT NULL,
    dependency_type character varying(255) DEFAULT 'MANDATORY'::character varying NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT permit_template_dependencies_dependency_type_check CHECK (((dependency_type)::text = ANY ((ARRAY['MANDATORY'::character varying, 'OPTIONAL'::character varying, 'RECOMMENDED'::character varying])::text[])))
);


ALTER TABLE public.permit_template_dependencies OWNER TO admin;

--
-- Name: permit_template_dependencies_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permit_template_dependencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permit_template_dependencies_id_seq OWNER TO admin;

--
-- Name: permit_template_dependencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permit_template_dependencies_id_seq OWNED BY public.permit_template_dependencies.id;


--
-- Name: permit_template_items; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permit_template_items (
    id bigint NOT NULL,
    template_id bigint NOT NULL,
    permit_type_id bigint,
    custom_permit_name character varying(100),
    sequence_order integer DEFAULT 0 NOT NULL,
    is_goal_permit boolean DEFAULT false NOT NULL,
    estimated_days integer,
    estimated_cost numeric(15,2),
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permit_template_items OWNER TO admin;

--
-- Name: permit_template_items_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permit_template_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permit_template_items_id_seq OWNER TO admin;

--
-- Name: permit_template_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permit_template_items_id_seq OWNED BY public.permit_template_items.id;


--
-- Name: permit_templates; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permit_templates (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    description text,
    use_case text,
    category character varying(50),
    created_by_user_id bigint,
    is_public boolean DEFAULT false NOT NULL,
    usage_count integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permit_templates OWNER TO admin;

--
-- Name: permit_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permit_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permit_templates_id_seq OWNER TO admin;

--
-- Name: permit_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permit_templates_id_seq OWNED BY public.permit_templates.id;


--
-- Name: permit_types; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.permit_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    code character varying(50) NOT NULL,
    category character varying(255) NOT NULL,
    institution_id bigint,
    avg_processing_days integer,
    description text,
    required_documents json,
    estimated_cost_min numeric(15,2),
    estimated_cost_max numeric(15,2),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT permit_types_category_check CHECK (((category)::text = ANY ((ARRAY['environmental'::character varying, 'land'::character varying, 'building'::character varying, 'transportation'::character varying, 'business'::character varying, 'other'::character varying])::text[])))
);


ALTER TABLE public.permit_types OWNER TO admin;

--
-- Name: permit_types_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.permit_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.permit_types_id_seq OWNER TO admin;

--
-- Name: permit_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.permit_types_id_seq OWNED BY public.permit_types.id;


--
-- Name: project_expenses; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.project_expenses (
    id bigint NOT NULL,
    project_id bigint,
    expense_date date NOT NULL,
    vendor_name character varying(255),
    amount numeric(15,2) NOT NULL,
    bank_account_id bigint,
    description text,
    receipt_file character varying(255),
    is_billable boolean DEFAULT true NOT NULL,
    created_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_receivable boolean DEFAULT false NOT NULL,
    receivable_from character varying(255),
    receivable_status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    receivable_paid_amount numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    receivable_notes text,
    category character varying(50) NOT NULL,
    payment_method character varying(50) DEFAULT 'transfer'::character varying NOT NULL,
    is_reconciled boolean DEFAULT false NOT NULL,
    reconciled_at timestamp(0) without time zone,
    reconciliation_id bigint,
    CONSTRAINT project_expenses_receivable_status_check CHECK (((receivable_status)::text = ANY ((ARRAY['pending'::character varying, 'partial'::character varying, 'paid'::character varying])::text[])))
);


ALTER TABLE public.project_expenses OWNER TO admin;

--
-- Name: COLUMN project_expenses.expense_date; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.expense_date IS 'Tanggal pengeluaran';


--
-- Name: COLUMN project_expenses.vendor_name; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.vendor_name IS 'Nama rekanan/penerima pembayaran';


--
-- Name: COLUMN project_expenses.amount; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.amount IS 'Nominal pengeluaran';


--
-- Name: COLUMN project_expenses.description; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.description IS 'Keterangan pengeluaran';


--
-- Name: COLUMN project_expenses.receipt_file; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.receipt_file IS 'Path file bukti pembayaran';


--
-- Name: COLUMN project_expenses.is_billable; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.is_billable IS 'Apakah bisa ditagihkan ke klien?';


--
-- Name: COLUMN project_expenses.is_receivable; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.is_receivable IS 'Apakah pengeluaran ini merupakan kasbon/piutang yang perlu dikembalikan oleh karyawan/pihak internal';


--
-- Name: COLUMN project_expenses.receivable_from; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.receivable_from IS 'Nama karyawan/pihak yang berhutang';


--
-- Name: COLUMN project_expenses.receivable_status; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.receivable_status IS 'Status pembayaran kasbon: pending, partial, paid';


--
-- Name: COLUMN project_expenses.receivable_paid_amount; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.receivable_paid_amount IS 'Jumlah yang sudah dibayar/dikembalikan';


--
-- Name: COLUMN project_expenses.receivable_notes; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_expenses.receivable_notes IS 'Catatan pembayaran kasbon';


--
-- Name: project_expenses_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.project_expenses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_expenses_id_seq OWNER TO admin;

--
-- Name: project_expenses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.project_expenses_id_seq OWNED BY public.project_expenses.id;


--
-- Name: project_logs; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.project_logs (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    user_id bigint,
    action character varying(255) NOT NULL,
    entity_type character varying(255),
    entity_id bigint,
    description text NOT NULL,
    old_values json,
    new_values json,
    notes text,
    ip_address character varying(45),
    user_agent character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.project_logs OWNER TO admin;

--
-- Name: COLUMN project_logs.action; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_logs.action IS 'created, updated, status_changed, document_uploaded, dll';


--
-- Name: COLUMN project_logs.entity_type; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_logs.entity_type IS 'project, task, document, dll';


--
-- Name: project_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.project_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_logs_id_seq OWNER TO admin;

--
-- Name: project_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.project_logs_id_seq OWNED BY public.project_logs.id;


--
-- Name: project_payments; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.project_payments (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    payment_date date NOT NULL,
    amount numeric(15,2) NOT NULL,
    payment_type character varying(255) DEFAULT 'other'::character varying NOT NULL,
    bank_account_id bigint,
    reference_number character varying(100),
    description text,
    receipt_file character varying(255),
    created_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    payment_method character varying(255) DEFAULT 'bank_transfer'::character varying NOT NULL,
    invoice_id bigint,
    is_reconciled boolean DEFAULT false NOT NULL,
    reconciled_at timestamp(0) without time zone,
    reconciliation_id bigint,
    CONSTRAINT project_payments_payment_method_check CHECK (((payment_method)::text = ANY ((ARRAY['cash'::character varying, 'bank_transfer'::character varying, 'check'::character varying, 'giro'::character varying, 'other'::character varying])::text[]))),
    CONSTRAINT project_payments_payment_type_check CHECK (((payment_type)::text = ANY ((ARRAY['dp'::character varying, 'progress'::character varying, 'final'::character varying, 'other'::character varying])::text[])))
);


ALTER TABLE public.project_payments OWNER TO admin;

--
-- Name: COLUMN project_payments.payment_date; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_payments.payment_date IS 'Tanggal terima pembayaran';


--
-- Name: COLUMN project_payments.amount; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_payments.amount IS 'Nominal pembayaran';


--
-- Name: COLUMN project_payments.reference_number; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_payments.reference_number IS 'Nomor referensi/bukti transfer';


--
-- Name: COLUMN project_payments.description; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_payments.description IS 'Keterangan tambahan';


--
-- Name: COLUMN project_payments.receipt_file; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_payments.receipt_file IS 'Path file bukti pembayaran';


--
-- Name: project_payments_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.project_payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_payments_id_seq OWNER TO admin;

--
-- Name: project_payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.project_payments_id_seq OWNED BY public.project_payments.id;


--
-- Name: project_permit_dependencies; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.project_permit_dependencies (
    id bigint NOT NULL,
    project_permit_id bigint NOT NULL,
    depends_on_permit_id bigint NOT NULL,
    dependency_type character varying(255) DEFAULT 'MANDATORY'::character varying NOT NULL,
    can_proceed_without boolean DEFAULT false NOT NULL,
    override_reason text,
    override_document_path character varying(255),
    overridden_by_user_id bigint,
    overridden_at timestamp(0) without time zone,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT project_permit_dependencies_dependency_type_check CHECK (((dependency_type)::text = ANY ((ARRAY['MANDATORY'::character varying, 'OPTIONAL'::character varying])::text[])))
);


ALTER TABLE public.project_permit_dependencies OWNER TO admin;

--
-- Name: project_permit_dependencies_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.project_permit_dependencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_permit_dependencies_id_seq OWNER TO admin;

--
-- Name: project_permit_dependencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.project_permit_dependencies_id_seq OWNED BY public.project_permit_dependencies.id;


--
-- Name: project_permits; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.project_permits (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    permit_type_id bigint,
    custom_permit_name character varying(100),
    custom_institution_name character varying(100),
    sequence_order integer DEFAULT 0 NOT NULL,
    is_goal_permit boolean DEFAULT false NOT NULL,
    status character varying(255) DEFAULT 'NOT_STARTED'::character varying NOT NULL,
    has_existing_document boolean DEFAULT false NOT NULL,
    existing_document_id bigint,
    assigned_to_user_id bigint,
    started_at timestamp(0) without time zone,
    submitted_at timestamp(0) without time zone,
    approved_at timestamp(0) without time zone,
    rejected_at timestamp(0) without time zone,
    target_date date,
    estimated_cost numeric(15,2),
    actual_cost numeric(15,2),
    permit_number character varying(100),
    valid_until date,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    override_dependencies boolean DEFAULT false NOT NULL,
    override_reason text,
    override_by_user_id bigint,
    override_at timestamp(0) without time zone,
    CONSTRAINT project_permits_status_check CHECK (((status)::text = ANY ((ARRAY['NOT_STARTED'::character varying, 'IN_PROGRESS'::character varying, 'WAITING_DOC'::character varying, 'SUBMITTED'::character varying, 'UNDER_REVIEW'::character varying, 'APPROVED'::character varying, 'REJECTED'::character varying, 'EXISTING'::character varying, 'CANCELLED'::character varying])::text[])))
);


ALTER TABLE public.project_permits OWNER TO admin;

--
-- Name: project_permits_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.project_permits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_permits_id_seq OWNER TO admin;

--
-- Name: project_permits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.project_permits_id_seq OWNED BY public.project_permits.id;


--
-- Name: project_statuses; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.project_statuses (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    code character varying(255) NOT NULL,
    description text,
    color character varying(7) DEFAULT '#6B7280'::character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    is_final boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.project_statuses OWNER TO admin;

--
-- Name: COLUMN project_statuses.is_final; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.project_statuses.is_final IS 'Status akhir/selesai';


--
-- Name: project_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.project_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_statuses_id_seq OWNER TO admin;

--
-- Name: project_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.project_statuses_id_seq OWNED BY public.project_statuses.id;


--
-- Name: projects; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.projects (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    client_name character varying(255) NOT NULL,
    client_address text,
    status_id bigint NOT NULL,
    institution_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    client_contact character varying(255),
    start_date date,
    deadline date,
    progress_percentage integer DEFAULT 0 NOT NULL,
    budget numeric(15,2),
    actual_cost numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    contract_value numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    down_payment numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    payment_received numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    total_expenses numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    profit_margin numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    payment_terms text,
    payment_status character varying(255) DEFAULT 'unpaid'::character varying NOT NULL,
    client_id bigint,
    CONSTRAINT projects_payment_status_check CHECK (((payment_status)::text = ANY ((ARRAY['unpaid'::character varying, 'partial'::character varying, 'paid'::character varying])::text[])))
);


ALTER TABLE public.projects OWNER TO admin;

--
-- Name: COLUMN projects.contract_value; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.projects.contract_value IS 'Nilai kontrak total';


--
-- Name: COLUMN projects.down_payment; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.projects.down_payment IS 'Uang muka (DP)';


--
-- Name: COLUMN projects.payment_received; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.projects.payment_received IS 'Total pembayaran diterima';


--
-- Name: COLUMN projects.total_expenses; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.projects.total_expenses IS 'Total pengeluaran';


--
-- Name: COLUMN projects.profit_margin; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.projects.profit_margin IS 'Profit margin (%)';


--
-- Name: COLUMN projects.payment_terms; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.projects.payment_terms IS 'Termin pembayaran';


--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO admin;

--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    display_name character varying(255) NOT NULL,
    description text,
    is_system boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO admin;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO admin;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: security_settings; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.security_settings (
    id bigint NOT NULL,
    min_password_length smallint DEFAULT '8'::smallint NOT NULL,
    require_special_char boolean DEFAULT true NOT NULL,
    require_number boolean DEFAULT true NOT NULL,
    require_mixed_case boolean DEFAULT true NOT NULL,
    enforce_password_expiration boolean DEFAULT false NOT NULL,
    password_expiration_days smallint DEFAULT '90'::smallint NOT NULL,
    session_timeout_minutes smallint DEFAULT '30'::smallint NOT NULL,
    allow_concurrent_sessions boolean DEFAULT true NOT NULL,
    two_factor_enabled boolean DEFAULT false NOT NULL,
    activity_log_enabled boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.security_settings OWNER TO admin;

--
-- Name: security_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.security_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.security_settings_id_seq OWNER TO admin;

--
-- Name: security_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.security_settings_id_seq OWNED BY public.security_settings.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO admin;

--
-- Name: system_settings; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.system_settings (
    id bigint NOT NULL,
    company_name character varying(255),
    company_email character varying(255),
    company_phone character varying(255),
    company_website character varying(255),
    company_address text,
    maintenance_mode boolean DEFAULT false NOT NULL,
    email_notifications boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.system_settings OWNER TO admin;

--
-- Name: system_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.system_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.system_settings_id_seq OWNER TO admin;

--
-- Name: system_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.system_settings_id_seq OWNED BY public.system_settings.id;


--
-- Name: tasks; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.tasks (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    sop_notes text,
    assigned_user_id bigint,
    due_date date,
    started_at timestamp(0) without time zone,
    completed_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'todo'::character varying NOT NULL,
    priority character varying(255) DEFAULT 'normal'::character varying NOT NULL,
    institution_id bigint,
    depends_on_task_id bigint,
    completion_notes text,
    estimated_hours integer,
    actual_hours integer,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    project_permit_id bigint,
    CONSTRAINT tasks_priority_check CHECK (((priority)::text = ANY ((ARRAY['low'::character varying, 'normal'::character varying, 'high'::character varying, 'urgent'::character varying])::text[]))),
    CONSTRAINT tasks_status_check CHECK (((status)::text = ANY ((ARRAY['todo'::character varying, 'in_progress'::character varying, 'done'::character varying, 'blocked'::character varying])::text[])))
);


ALTER TABLE public.tasks OWNER TO admin;

--
-- Name: COLUMN tasks.sop_notes; Type: COMMENT; Schema: public; Owner: admin
--

COMMENT ON COLUMN public.tasks.sop_notes IS 'SOP atau checklist tugas';


--
-- Name: tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tasks_id_seq OWNER TO admin;

--
-- Name: tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.tasks_id_seq OWNED BY public.tasks.id;


--
-- Name: tax_rates; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.tax_rates (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    rate numeric(5,2) NOT NULL,
    description character varying(255),
    is_default boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tax_rates OWNER TO admin;

--
-- Name: tax_rates_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.tax_rates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tax_rates_id_seq OWNER TO admin;

--
-- Name: tax_rates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.tax_rates_id_seq OWNED BY public.tax_rates.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: admin
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    full_name character varying(255),
    "position" character varying(255),
    phone character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    last_login_at timestamp(0) without time zone,
    notes text,
    avatar character varying(255),
    role_id bigint
);


ALTER TABLE public.users OWNER TO admin;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: admin
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO admin;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: admin
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: ai_processing_logs id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ai_processing_logs ALTER COLUMN id SET DEFAULT nextval('public.ai_processing_logs_id_seq'::regclass);


--
-- Name: articles id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.articles ALTER COLUMN id SET DEFAULT nextval('public.articles_id_seq'::regclass);


--
-- Name: bank_reconciliations id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_reconciliations ALTER COLUMN id SET DEFAULT nextval('public.bank_reconciliations_id_seq'::regclass);


--
-- Name: bank_statement_entries id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_statement_entries ALTER COLUMN id SET DEFAULT nextval('public.bank_statement_entries_id_seq'::regclass);


--
-- Name: cash_accounts id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cash_accounts ALTER COLUMN id SET DEFAULT nextval('public.cash_accounts_id_seq'::regclass);


--
-- Name: clients id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients ALTER COLUMN id SET DEFAULT nextval('public.clients_id_seq'::regclass);


--
-- Name: compliance_checks id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.compliance_checks ALTER COLUMN id SET DEFAULT nextval('public.compliance_checks_id_seq'::regclass);


--
-- Name: document_drafts id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts ALTER COLUMN id SET DEFAULT nextval('public.document_drafts_id_seq'::regclass);


--
-- Name: document_templates id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_templates ALTER COLUMN id SET DEFAULT nextval('public.document_templates_id_seq'::regclass);


--
-- Name: documents id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.documents ALTER COLUMN id SET DEFAULT nextval('public.documents_id_seq'::regclass);


--
-- Name: email_campaigns id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_campaigns ALTER COLUMN id SET DEFAULT nextval('public.email_campaigns_id_seq'::regclass);


--
-- Name: email_inbox id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_inbox ALTER COLUMN id SET DEFAULT nextval('public.email_inbox_id_seq'::regclass);


--
-- Name: email_logs id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_logs ALTER COLUMN id SET DEFAULT nextval('public.email_logs_id_seq'::regclass);


--
-- Name: email_subscribers id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_subscribers ALTER COLUMN id SET DEFAULT nextval('public.email_subscribers_id_seq'::regclass);


--
-- Name: email_templates id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_templates ALTER COLUMN id SET DEFAULT nextval('public.email_templates_id_seq'::regclass);


--
-- Name: expense_categories id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.expense_categories ALTER COLUMN id SET DEFAULT nextval('public.expense_categories_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: institutions id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.institutions ALTER COLUMN id SET DEFAULT nextval('public.institutions_id_seq'::regclass);


--
-- Name: invoice_items id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoice_items ALTER COLUMN id SET DEFAULT nextval('public.invoice_items_id_seq'::regclass);


--
-- Name: invoices id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoices ALTER COLUMN id SET DEFAULT nextval('public.invoices_id_seq'::regclass);


--
-- Name: job_applications id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_applications ALTER COLUMN id SET DEFAULT nextval('public.job_applications_id_seq'::regclass);


--
-- Name: job_vacancies id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_vacancies ALTER COLUMN id SET DEFAULT nextval('public.job_vacancies_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: milestones id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.milestones ALTER COLUMN id SET DEFAULT nextval('public.milestones_id_seq'::regclass);


--
-- Name: payment_methods id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_methods ALTER COLUMN id SET DEFAULT nextval('public.payment_methods_id_seq'::regclass);


--
-- Name: payment_schedules id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_schedules ALTER COLUMN id SET DEFAULT nextval('public.payment_schedules_id_seq'::regclass);


--
-- Name: permission_role id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permission_role ALTER COLUMN id SET DEFAULT nextval('public.permission_role_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: permit_documents id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_documents ALTER COLUMN id SET DEFAULT nextval('public.permit_documents_id_seq'::regclass);


--
-- Name: permit_template_dependencies id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_dependencies ALTER COLUMN id SET DEFAULT nextval('public.permit_template_dependencies_id_seq'::regclass);


--
-- Name: permit_template_items id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_items ALTER COLUMN id SET DEFAULT nextval('public.permit_template_items_id_seq'::regclass);


--
-- Name: permit_templates id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_templates ALTER COLUMN id SET DEFAULT nextval('public.permit_templates_id_seq'::regclass);


--
-- Name: permit_types id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_types ALTER COLUMN id SET DEFAULT nextval('public.permit_types_id_seq'::regclass);


--
-- Name: project_expenses id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_expenses ALTER COLUMN id SET DEFAULT nextval('public.project_expenses_id_seq'::regclass);


--
-- Name: project_logs id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_logs ALTER COLUMN id SET DEFAULT nextval('public.project_logs_id_seq'::regclass);


--
-- Name: project_payments id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments ALTER COLUMN id SET DEFAULT nextval('public.project_payments_id_seq'::regclass);


--
-- Name: project_permit_dependencies id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies ALTER COLUMN id SET DEFAULT nextval('public.project_permit_dependencies_id_seq'::regclass);


--
-- Name: project_permits id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits ALTER COLUMN id SET DEFAULT nextval('public.project_permits_id_seq'::regclass);


--
-- Name: project_statuses id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_statuses ALTER COLUMN id SET DEFAULT nextval('public.project_statuses_id_seq'::regclass);


--
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: security_settings id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.security_settings ALTER COLUMN id SET DEFAULT nextval('public.security_settings_id_seq'::regclass);


--
-- Name: system_settings id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.system_settings ALTER COLUMN id SET DEFAULT nextval('public.system_settings_id_seq'::regclass);


--
-- Name: tasks id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks ALTER COLUMN id SET DEFAULT nextval('public.tasks_id_seq'::regclass);


--
-- Name: tax_rates id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tax_rates ALTER COLUMN id SET DEFAULT nextval('public.tax_rates_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: ai_processing_logs; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.ai_processing_logs (id, document_id, template_id, project_id, operation_type, status, input_tokens, output_tokens, cost, error_message, metadata, initiated_by, created_at, updated_at) FROM stdin;
1	\N	1	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location, land_area, land_certificate, pic_name	{"started_at":"2025-11-03T13:53:56+00:00","failed_at":"2025-11-03T13:53:56+00:00","duration_seconds":0.02}	1	2025-11-03 13:53:56	2025-11-03 13:53:56
2	\N	1	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location, land_area, land_certificate, pic_name	{"started_at":"2025-11-03T13:53:56+00:00","failed_at":"2025-11-03T13:53:56+00:00","duration_seconds":0}	1	2025-11-03 13:53:56	2025-11-03 13:53:56
3	\N	1	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location, land_area, land_certificate, pic_name	{"started_at":"2025-11-03T13:53:56+00:00","failed_at":"2025-11-03T13:53:56+00:00","duration_seconds":0}	1	2025-11-03 13:53:56	2025-11-03 13:53:56
4	\N	1	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location, land_area, land_certificate, pic_name	{"started_at":"2025-11-03T13:56:02+00:00","failed_at":"2025-11-03T13:56:02+00:00","duration_seconds":0}	1	2025-11-03 13:56:02	2025-11-03 13:56:02
5	\N	1	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location, land_area, land_certificate, pic_name	{"started_at":"2025-11-03T13:56:02+00:00","failed_at":"2025-11-03T13:56:02+00:00","duration_seconds":0}	1	2025-11-03 13:56:02	2025-11-03 13:56:02
6	\N	1	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location, land_area, land_certificate, pic_name	{"started_at":"2025-11-03T13:56:02+00:00","failed_at":"2025-11-03T13:56:02+00:00","duration_seconds":0}	1	2025-11-03 13:56:02	2025-11-03 13:56:02
7	\N	3	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location	{"started_at":"2025-11-03T13:56:44+00:00","failed_at":"2025-11-03T13:56:44+00:00","duration_seconds":0}	1	2025-11-03 13:56:44	2025-11-03 13:56:44
8	\N	3	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location	{"started_at":"2025-11-03T13:56:44+00:00","failed_at":"2025-11-03T13:56:44+00:00","duration_seconds":0}	1	2025-11-03 13:56:44	2025-11-03 13:56:44
9	\N	3	1	paraphrase	failed	\N	\N	\N	Missing required fields: client_name, location	{"started_at":"2025-11-03T13:56:44+00:00","failed_at":"2025-11-03T13:56:44+00:00","duration_seconds":0}	1	2025-11-03 13:56:44	2025-11-03 13:56:44
10	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T13:59:41+00:00","completed_at":"2025-11-03T13:59:42+00:00","duration_seconds":0.12,"chunks_count":1,"draft_id":1,"word_count":53,"page_count":null}	1	2025-11-03 13:59:41	2025-11-03 13:59:42
11	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:04:26+00:00","completed_at":"2025-11-03T14:04:26+00:00","duration_seconds":0.16,"chunks_count":1,"draft_id":2,"word_count":53,"page_count":null}	1	2025-11-03 14:04:26	2025-11-03 14:04:26
12	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:06:39+00:00","completed_at":"2025-11-03T14:06:40+00:00","duration_seconds":0.21,"chunks_count":1,"draft_id":3,"word_count":53,"page_count":null}	1	2025-11-03 14:06:39	2025-11-03 14:06:40
13	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:06:52+00:00","completed_at":"2025-11-03T14:06:52+00:00","duration_seconds":0.17,"chunks_count":1,"draft_id":4,"word_count":53,"page_count":null}	1	2025-11-03 14:06:52	2025-11-03 14:06:52
14	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:08:00+00:00","completed_at":"2025-11-03T14:08:00+00:00","duration_seconds":0.15,"chunks_count":1,"draft_id":5,"word_count":53,"page_count":null}	1	2025-11-03 14:08:00	2025-11-03 14:08:00
15	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:08:53+00:00","completed_at":"2025-11-03T14:08:54+00:00","duration_seconds":0.14,"chunks_count":1,"draft_id":6,"word_count":53,"page_count":null}	1	2025-11-03 14:08:53	2025-11-03 14:08:54
16	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:09:31+00:00","completed_at":"2025-11-03T14:09:31+00:00","duration_seconds":0.19,"chunks_count":1,"draft_id":7,"word_count":53,"page_count":null}	1	2025-11-03 14:09:31	2025-11-03 14:09:31
17	\N	1	1	paraphrase	completed	0	0	0.000000	\N	{"started_at":"2025-11-03T14:09:43+00:00","completed_at":"2025-11-03T14:09:43+00:00","duration_seconds":0.1,"chunks_count":1,"draft_id":8,"word_count":53,"page_count":null}	1	2025-11-03 14:09:43	2025-11-03 14:09:43
18	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:10:54+00:00","failed_at":"2025-11-03T14:10:55+00:00","duration_seconds":0.19}	1	2025-11-03 14:10:54	2025-11-03 14:10:55
19	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:10:55+00:00","failed_at":"2025-11-03T14:10:55+00:00","duration_seconds":0.14}	1	2025-11-03 14:10:55	2025-11-03 14:10:55
20	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:10:55+00:00","failed_at":"2025-11-03T14:10:55+00:00","duration_seconds":0.13}	1	2025-11-03 14:10:55	2025-11-03 14:10:55
21	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:12:40+00:00","failed_at":"2025-11-03T14:12:40+00:00","duration_seconds":0.21}	1	2025-11-03 14:12:40	2025-11-03 14:12:40
22	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:12:40+00:00","failed_at":"2025-11-03T14:12:40+00:00","duration_seconds":0.12}	1	2025-11-03 14:12:40	2025-11-03 14:12:40
23	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:12:40+00:00","failed_at":"2025-11-03T14:12:40+00:00","duration_seconds":0.15}	1	2025-11-03 14:12:40	2025-11-03 14:12:40
24	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:12:43+00:00","failed_at":"2025-11-03T14:12:43+00:00","duration_seconds":0.17}	1	2025-11-03 14:12:43	2025-11-03 14:12:43
25	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:12:43+00:00","failed_at":"2025-11-03T14:12:43+00:00","duration_seconds":0.12}	1	2025-11-03 14:12:43	2025-11-03 14:12:43
26	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:12:44+00:00","failed_at":"2025-11-03T14:12:44+00:00","duration_seconds":0.15}	1	2025-11-03 14:12:44	2025-11-03 14:12:44
27	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:14:08+00:00","failed_at":"2025-11-03T14:14:09+00:00","duration_seconds":0.2}	1	2025-11-03 14:14:08	2025-11-03 14:14:09
28	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:14:09+00:00","failed_at":"2025-11-03T14:14:09+00:00","duration_seconds":0.17}	1	2025-11-03 14:14:09	2025-11-03 14:14:09
29	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:14:09+00:00","failed_at":"2025-11-03T14:14:09+00:00","duration_seconds":0.17}	1	2025-11-03 14:14:09	2025-11-03 14:14:09
30	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:14:18+00:00","failed_at":"2025-11-03T14:14:18+00:00","duration_seconds":0.13}	1	2025-11-03 14:14:18	2025-11-03 14:14:18
31	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:14:18+00:00","failed_at":"2025-11-03T14:14:18+00:00","duration_seconds":0.18}	1	2025-11-03 14:14:18	2025-11-03 14:14:18
32	\N	1	1	paraphrase	failed	\N	\N	\N	AI paraphrasing failed: Invalid JSON response from OpenRouter	{"started_at":"2025-11-03T14:14:18+00:00","failed_at":"2025-11-03T14:14:19+00:00","duration_seconds":0.29}	1	2025-11-03 14:14:18	2025-11-03 14:14:19
33	\N	1	1	paraphrase	completed	903	1109	0.000000	\N	{"started_at":"2025-11-03T14:16:35+00:00","completed_at":"2025-11-03T14:16:44+00:00","duration_seconds":9.48,"chunks_count":1,"draft_id":9,"word_count":53,"page_count":null}	1	2025-11-03 14:16:35	2025-11-03 14:16:44
34	\N	2	1	paraphrase	completed	929	851	0.000000	\N	{"started_at":"2025-11-03T14:18:29+00:00","completed_at":"2025-11-03T14:18:39+00:00","duration_seconds":9.55,"chunks_count":1,"draft_id":10,"word_count":56,"page_count":null}	1	2025-11-03 14:18:29	2025-11-03 14:18:39
35	\N	1	1	paraphrase	completed	903	1382	0.000000	\N	{"started_at":"2025-11-03T14:20:26+00:00","completed_at":"2025-11-03T14:20:36+00:00","duration_seconds":10.49,"chunks_count":1,"draft_id":11,"word_count":53,"page_count":null}	1	2025-11-03 14:20:26	2025-11-03 14:20:36
36	\N	1	1	paraphrase	completed	903	850	0.000000	\N	{"started_at":"2025-11-03T14:20:36+00:00","completed_at":"2025-11-03T14:20:41+00:00","duration_seconds":5.11,"chunks_count":1,"draft_id":12,"word_count":53,"page_count":null}	1	2025-11-03 14:20:36	2025-11-03 14:20:41
37	\N	3	1	paraphrase	failed	\N	\N	\N	Missing required fields: building_area, land_certificate	{"started_at":"2025-11-03T16:26:50+00:00","failed_at":"2025-11-03T16:26:50+00:00","duration_seconds":0.01}	1	2025-11-03 16:26:50	2025-11-03 16:26:50
38	\N	3	1	paraphrase	failed	\N	\N	\N	Missing required fields: building_area, land_certificate	{"started_at":"2025-11-03T16:26:50+00:00","failed_at":"2025-11-03T16:26:50+00:00","duration_seconds":0}	1	2025-11-03 16:26:50	2025-11-03 16:26:50
39	\N	3	1	paraphrase	failed	\N	\N	\N	Missing required fields: building_area, land_certificate	{"started_at":"2025-11-03T16:26:50+00:00","failed_at":"2025-11-03T16:26:50+00:00","duration_seconds":0}	1	2025-11-03 16:26:50	2025-11-03 16:26:50
40	\N	1	1	paraphrase	completed	500	1000	0.001000	\N	\N	1	2025-11-03 17:12:47	2025-11-03 17:12:47
41	\N	2	1	paraphrase	failed	\N	\N	\N	Missing required fields: business_type, land_area, building_area	{"started_at":"2025-11-03T17:24:46+00:00","failed_at":"2025-11-03T17:24:46+00:00","duration_seconds":0.01}	1	2025-11-03 17:24:46	2025-11-03 17:24:46
42	\N	2	1	paraphrase	failed	\N	\N	\N	Missing required fields: business_type, land_area, building_area	{"started_at":"2025-11-03T17:24:46+00:00","failed_at":"2025-11-03T17:24:46+00:00","duration_seconds":0}	1	2025-11-03 17:24:46	2025-11-03 17:24:46
43	\N	2	1	paraphrase	failed	\N	\N	\N	Missing required fields: business_type, land_area, building_area	{"started_at":"2025-11-03T17:24:46+00:00","failed_at":"2025-11-03T17:24:46+00:00","duration_seconds":0}	1	2025-11-03 17:24:46	2025-11-03 17:24:46
\.


--
-- Data for Name: articles; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.articles (id, title, slug, excerpt, content, featured_image, category, tags, status, published_at, views_count, author_id, meta_title, meta_description, meta_keywords, is_featured, reading_time, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: bank_reconciliations; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.bank_reconciliations (id, cash_account_id, reconciliation_date, start_date, end_date, opening_balance_book, opening_balance_bank, closing_balance_book, closing_balance_bank, total_deposits_in_transit, total_outstanding_checks, total_bank_charges, total_bank_credits, adjusted_bank_balance, adjusted_book_balance, difference, status, bank_statement_file, bank_statement_format, notes, reconciled_by, reviewed_by, approved_by, completed_at, reviewed_at, approved_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: bank_statement_entries; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.bank_statement_entries (id, reconciliation_id, transaction_date, description, debit_amount, credit_amount, running_balance, reference_number, is_matched, matched_transaction_type, matched_transaction_id, match_confidence, match_notes, unmatch_reason, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.cache (key, value, expiration) FROM stdin;
laravel-cache-dashboard_data_1	YTo5OntzOjE0OiJjcml0aWNhbEFsZXJ0cyI7YTo4OntzOjE2OiJvdmVyZHVlX3Byb2plY3RzIjtPOjM5OiJJbGx1bWluYXRlXERhdGFiYXNlXEVsb3F1ZW50XENvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MTp7aTowO086MTg6IkFwcFxNb2RlbHNcUHJvamVjdCI6MzM6e3M6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToicGdzcWwiO3M6ODoiACoAdGFibGUiO3M6ODoicHJvamVjdHMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YToyNTp7czoyOiJpZCI7aTo0O3M6NDoibmFtZSI7czozMToiU2VydGlmaWthdCBIYWxhbCBQcm9kdWsgTWFrYW5hbiI7czoxMToiZGVzY3JpcHRpb24iO3M6NjU6IlBlbmd1cnVzYW4gc2VydGlmaWthdCBoYWxhbCB1bnR1ayAxNSB2YXJpYW4gcHJvZHVrIG1ha2FuYW4gb2xhaGFuIjtzOjExOiJjbGllbnRfbmFtZSI7czoxNzoiVUQgQmVya2FoIE1hbmRpcmkiO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjMyOiJKbC4gTWFsaW9ib3JvIE5vLiA0NSwgWW9neWFrYXJ0YSI7czo5OiJzdGF0dXNfaWQiO2k6NDtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aTo0O3M6NToibm90ZXMiO3M6ODI6IlNlcnRpZmlrYXQgaGFsYWwgdGVsYWggZGl0ZXJiaXRrYW4gdW50dWsgc2VtdWEgcHJvZHVrLiBQcm9zZXMgc2VsZXNhaSB0ZXBhdCB3YWt0dS4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjM4OiIwMjc0LTY1NDMyMSAvIGJlcmthaC5tYW5kaXJpQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0wNy0wMSI7czo4OiJkZWFkbGluZSI7czoxMDoiMjAyNS0wOS0zMCI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aToxMDA7czo2OiJidWRnZXQiO3M6MTE6Ijg1MDAwMDAwLjAwIjtzOjExOiJhY3R1YWxfY29zdCI7czoxMToiODAwMDAwMDAuMDAiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjQ6IjAuMDAiO3M6MTI6ImRvd25fcGF5bWVudCI7czo0OiIwLjAwIjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtzOjQ6IjAuMDAiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjQ6IjAuMDAiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6NDoiMC4wMCI7czoxMzoicGF5bWVudF90ZXJtcyI7TjtzOjE0OiJwYXltZW50X3N0YXR1cyI7czo2OiJ1bnBhaWQiO3M6OToiY2xpZW50X2lkIjtOO3M6MTI6ImRheXNfb3ZlcmR1ZSI7ZDotNDQ7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjI0OntzOjI6ImlkIjtpOjQ7czo0OiJuYW1lIjtzOjMxOiJTZXJ0aWZpa2F0IEhhbGFsIFByb2R1ayBNYWthbmFuIjtzOjExOiJkZXNjcmlwdGlvbiI7czo2NToiUGVuZ3VydXNhbiBzZXJ0aWZpa2F0IGhhbGFsIHVudHVrIDE1IHZhcmlhbiBwcm9kdWsgbWFrYW5hbiBvbGFoYW4iO3M6MTE6ImNsaWVudF9uYW1lIjtzOjE3OiJVRCBCZXJrYWggTWFuZGlyaSI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6MzI6IkpsLiBNYWxpb2Jvcm8gTm8uIDQ1LCBZb2d5YWthcnRhIjtzOjk6InN0YXR1c19pZCI7aTo0O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjQ7czo1OiJub3RlcyI7czo4MjoiU2VydGlmaWthdCBoYWxhbCB0ZWxhaCBkaXRlcmJpdGthbiB1bnR1ayBzZW11YSBwcm9kdWsuIFByb3NlcyBzZWxlc2FpIHRlcGF0IHdha3R1LiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzg6IjAyNzQtNjU0MzIxIC8gYmVya2FoLm1hbmRpcmlAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTA3LTAxIjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI1LTA5LTMwIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjEwMDtzOjY6ImJ1ZGdldCI7czoxMToiODUwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjExOiI4MDAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTE6IgAqAHByZXZpb3VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjEwOntzOjEwOiJzdGFydF9kYXRlIjtzOjQ6ImRhdGUiO3M6ODoiZGVhZGxpbmUiO3M6NDoiZGF0ZSI7czo2OiJidWRnZXQiO3M6OToiZGVjaW1hbDoyIjtzOjExOiJhY3R1YWxfY29z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OjMyOiJOb3RhcmlzIEFuZHJpIFdpamF5YSwgUy5ILiwgTS5LbiI7czo0OiJ0eXBlIjtzOjc6Ik5vdGFyaXMiO3M6NzoiYWRkcmVzcyI7czoyNDoiSmwuIEh1a3VtIE5vLiAzLCBKYWthcnRhIjtzOjU6InBob25lIjtzOjExOiIwMjEtMzQ1Njc4OSI7czo1OiJlbWFpbCI7czoyNToibm90YXJpcy5hbmRyaUBleGFtcGxlLmNvbSI7czoxNDoiY29udGFjdF9wZXJzb24iO3M6MTI6IkFuZHJpIFdpamF5YSI7czoxNjoiY29udGFjdF9wb3NpdGlvbiI7czo3OiJOb3RhcmlzIjtzOjU6Im5vdGVzIjtzOjQ0OiJVbnR1ayBha3RhIG5vdGFyaXMgZGFuIHBlbmRpcmlhbiBiYWRhbiB1c2FoYSI7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMToiACoAb3JpZ2luYWwiO2E6MTI6e3M6MjoiaWQiO2k6NDtzOjQ6Im5hbWUiO3M6MzI6Ik5vdGFyaXMgQW5kcmkgV2lqYXlhLCBTLkguLCBNLktuIjtzOjQ6InR5cGUiO3M6NzoiTm90YXJpcyI7czo3OiJhZGRyZXNzIjtzOjI0OiJKbC4gSHVrdW0gTm8uIDMsIEpha2FydGEiO3M6NToicGhvbmUiO3M6MTE6IjAyMS0zNDU2Nzg5IjtzOjU6ImVtYWlsIjtzOjI1OiJub3RhcmlzLmFuZHJpQGV4YW1wbGUuY29tIjtzOjE0OiJjb250YWN0X3BlcnNvbiI7czoxMjoiQW5kcmkgV2lqYXlhIjtzOjE2OiJjb250YWN0X3Bvc2l0aW9uIjtzOjc6Ik5vdGFyaXMiO3M6NToibm90ZXMiO3M6NDQ6IlVudHVrIGFrdGEgbm90YXJpcyBkYW4gcGVuZGlyaWFuIGJhZGFuIHVzYWhhIjtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6MTA6InVwZGF0ZWRf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ACoAa2V5VHlwZSI7czozOiJpbnQiO3M6MTI6ImluY3JlbWVudGluZyI7YjoxO3M6NzoiACoAd2l0aCI7YTowOnt9czoxMjoiACoAd2l0aENvdW50IjthOjA6e31zOjE5OiJwcmV2ZW50c0xhenlMb2FkaW5nIjtiOjA7czoxMDoiACoAcGVyUGFnZSI7aToxNTtzOjY6ImV4aXN0cyI7YjoxO3M6MTg6Indhc1JlY2VudGx5Q3JlYXRlZCI7YjowO3M6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDtzOjEzOiIAKgBhdHRyaWJ1dGVzIjthOjIxOntzOjI6ImlkIjtpOjc7czoxMDoicHJvamVjdF9pZCI7aToxO3M6NToidGl0bGUiO3M6MjA6IlBlbmdhanVhbiBrZSBEUE1QVFNQIjtzOjExOiJkZXNjcmlwdGlvbiI7czo3ODoiUGVuZ2FqdWFuIGJlcmthcyBJTUIga2UgRGluYXMgUGVuYW5hbWFuIE1vZGFsIGRhbiBQZWxheWFuYW4gVGVycGFkdSBTYXR1IFBpbnR1IjtzOjk6InNvcF9ub3RlcyI7TjtzOjE2OiJhc3NpZ25lZF91c2VyX2lkIjtpOjQ7czo4OiJkdWVfZGF0ZSI7czoxMDoiMjAyNS0xMS0wMSI7czoxMDoic3RhcnRlZF9hdCI7TjtzOjEyOiJjb21wbGV0ZWRfYXQiO047czo2OiJzdGF0dXMiO3M6NDoidG9kbyI7czo4OiJwcmlvcml0eSI7czo2OiJ1cmdlbnQiO3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjY7czoxODoiZGVwZW5kc19vbl90YXNrX2lkIjtOO3M6MTY6ImNvbXBsZXRpb25fbm90ZXMiO047czoxNToiZXN0aW1hdGVkX2hvdXJzIjtpOjM7czoxMjoiYWN0dWFsX2hvdXJzIjtOO3M6MTA6InNvcnRfb3JkZXIiO2k6MztzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjE3OiJwcm9qZWN0X3Blcm1pdF9pZCI7TjtzOjEyOiJkYXlzX292ZXJkdWUi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b2t1bWVuIHRla25pcyBzdWRhaCBkaXNlcmFoa2FuLiBNZW51bmdndSB2ZXJpZmlrYXNpIGxhcGFuZ2FuIGRhcmkgZGluYXMuIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjE0OiJjbGllbnRfY29udGFjdCI7czozNDoiMDIxLTc2NTQzMjEgLyBtZWdhLnByaW1hQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0wOS0xNSI7czo4OiJkZWFkbGluZSI7czoxMDoiMjAyNS0xMi0xNSI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aTo2NTtzOjY6ImJ1ZGdldCI7czoxMjoiMjUwMDAwMDAwLjAwIjtzOjExOiJhY3R1YWxfY29zdCI7czoxMjoiMTUwMDAwMDAwLjAwIjtzOjE0OiJjb250cmFjdF92YWx1ZSI7czo0OiIwLjAwIjtzOjEyOiJkb3duX3BheW1lbnQiO3M6NDoiMC4wMCI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo0OiIwLjAwIjtzOjE0OiJ0b3RhbF9leHBlbnNlcyI7czo0OiIwLjAwIjtzOjEzOiJwcm9maXRfbWFyZ2luIjtzOjQ6IjAuMDAiO3M6MTM6InBheW1lbnRfdGVybXMiO047czoxNDoicGF5bWVudF9zdGF0dXMiO3M6NjoidW5wYWlkIjtzOjk6ImNsaWVudF9pZCI7Tjt9czoxMToiACoAb3JpZ2luYWwiO2E6MjQ6e3M6MjoiaWQiO2k6MTtzOjQ6Im5hbWUiO3M6MzI6IlBlcml6aW5hbiBJTUIgR2VkdW5nIFBlcmthbnRvcmFuIjtzOjExOiJkZXNjcmlwdGlvbiI7czoxMDM6IlBlbmd1cnVzYW4gSXppbiBNZW5kaXJpa2FuIEJhbmd1bmFuIHVudHVrIGdlZHVuZyBwZXJrYW50b3JhbiA4IGxhbnRhaSBkaSBrYXdhc2FuIGJpc25pcyBKYWthcnRhIFNlbGF0YW4iO3M6MTE6ImNsaWVudF9uYW1lIjtzOjI1OiJQVCBNZWdhIFByaW1hIERldmVsb3BtZW50IjtzOjE0OiJjbGllbnRfYWRkcmVzcyI7czozNzoiSmwuIFN1ZGlybWFuIE5vLiAxMjMsIEpha2FydGEgU2VsYXRhbiI7czo5OiJzdGF0dXNfaWQiO2k6MjtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aToxO3M6NToibm90ZXMiO3M6NzM6IkRva3VtZW4gdGVrbmlzIHN1ZGFoIGRpc2VyYWhrYW4uIE1lbnVuZ2d1IHZlcmlmaWthc2kgbGFwYW5nYW4gZGFyaSBkaW5hcy4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjM0OiIwMjEtNzY1NDMyMSAvIG1lZ2EucHJpbWFAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTA5LTE1IjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI1LTEyLTE1IjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjY1O3M6NjoiYnVkZ2V0IjtzOjEyOiIyNTAwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjEyOiIxNTAwMDAwMDAuMDAiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjQ6IjAuMDAiO3M6MTI6ImRvd25fcGF5bWVudCI7czo0OiIwLjAwIjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtzOjQ6IjAuMDAiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjQ6IjAuMDAiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6NDoiMC4wMCI7czoxMzoicGF5bWVudF90ZXJtcyI7TjtzOjE0OiJwYXltZW50X3N0YXR1cyI7czo2OiJ1bnBhaWQiO3M6OToiY2xpZW50X2lkIjtOO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjExOiIAKgBwcmV2aW91cyI7YTowOnt9czo4OiIAKgBjYXN0cyI7YToxMDp7czoxMDoic3RhcnRfZGF0ZSI7czo0OiJkYXRlIjtzOjg6ImRlYWRsaW5lIjtzOjQ6ImRhdGUiO3M6NjoiYnVkZ2V0IjtzOjk6ImRlY2ltYWw6MiI7czoxMToiYWN0dWFsX2Nvc3QiO3M6OToiZGVjaW1hbDoyIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtzOjc6ImludGVnZXIiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjk6ImRlY2ltYWw6MiI7czoxMjoiZG93bl9wYXltZW50IjtzOjk6ImRlY2ltYWw6MiI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo5OiJkZWNpbWFsOjIiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjk6ImRlY2ltYWw6MiI7czoxMzoicHJvZml0X21hcmdpbiI7czo5OiJkZWNpbWFsOjIiO31zOjE3OiIAKgBjbGFzc0Nhc3RDYWNoZSI7YTowOnt9czoyMToiACoAYXR0cmlidXRlQ2FzdENhY2hlIjthOjA6e31zOjEzOiIAKgBkYXRlRm9ybWF0IjtOO3M6MTA6IgAqAGFwcGVuZHMiO2E6MDp7fXM6MTk6IgAqAGRpc3BhdGNoZXNFdmVudHMiO2E6MDp7fXM6MTQ6IgAqAG9ic2VydmFibGVzIjthOjA6e31zOjEyOiIAKgByZWxhdGlvbnMiO2E6MDp7fXM6MTA6IgAqAHRvdWNoZXMiO2E6MDp7fXM6Mjc6IgAqAHJlbGF0aW9uQXV0b2xvYWRDYWxsYmFjayI7TjtzOjI2OiIAKgByZWxhdGlvbkF1dG9sb2FkQ29udGV4dCI7TjtzOjEwOiJ0aW1lc3RhbXBzIjtiOjE7czoxMzoidXNlc1VuaXF1ZUlkcyI7YjowO3M6OToiACoAaGlkZGVuIjthOjA6e31zOjEwOiIAKgB2aXNpYmxlIjthOjA6e31zOjExOiIAKgBmaWxsYWJsZSI7YToyMTp7aTowO3M6NDoibmFtZSI7aToxO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjI7czo5OiJjbGllbnRfaWQiO2k6MztzOjExOiJjbGllbnRfbmFtZSI7aTo0O3M6MTQ6ImNsaWVudF9jb250YWN0IjtpOjU7czoxNDoiY2xpZW50X2FkZHJlc3MiO2k6NjtzOjk6InN0YXR1c19pZCI7aTo3O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjg7czoxMDoic3RhcnRfZGF0ZSI7aTo5O3M6ODoiZGVhZGxpbmUiO2k6MTA7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aToxMTtzOjY6ImJ1ZGdldCI7aToxMjtzOjExOiJhY3R1YWxfY29zdCI7aToxMztzOjU6Im5vdGVzIjtpOjE0O3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtpOjE1O3M6MTI6ImRvd25fcGF5bWVudCI7aToxNjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtpOjE3O3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtpOjE4O3M6MTM6InByb2ZpdF9tYXJnaW4iO2k6MTk7czoxMzoicGF5bWVudF90ZXJtcyI7aToyMDtzOjE0OiJwYXltZW50X3N0YXR1cyI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX1zOjEyOiJhc3NpZ25lZFVzZXIiO086MTU6IkFwcFxNb2RlbHNcVXNlciI6MzU6e3M6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToicGdzcWwiO3M6ODoiACoAdGFibGUiO3M6NToidXNlcnMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YToxNjp7czoyOiJpZCI7aTo0O3M6NDoibmFtZSI7czo2OiJzdGFmZjIiO3M6NToiZW1haWwiO3M6MTY6ImFobWFkQGJpem1hcmsuaWQiO3M6MTc6ImVtYWlsX3ZlcmlmaWVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjg6InBhc3N3b3JkIjtzOjYwOiIkMnkkMTIkOW00ckVXOXhMTkliczlPMzRzRjl3Lk1SUTlkVU5EZzRPc3YzaWNkaWQ3WTJPeXkycDQvZUsiO3M6MTQ6InJlbWVtYmVyX3Rva2VuIjtOO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6OToiZnVsbF9uYW1lIjtzOjExOiJBaG1hZCBGYWRsaSI7czo4OiJwb3NpdGlvbiI7czoxNjoiS29uc3VsdGFuIEp1bmlvciI7czo1OiJwaG9uZSI7czoxMjoiMDgxMjM0NTY3ODkzIjtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6MTM6Imxhc3RfbG9naW5fYXQiO047czo1OiJub3RlcyI7czozMToiS29uc3VsdGFuIHBlcml6aW5hbiBsYWx1IGxpbnRhcyI7czo2OiJhdmF0YXIiO047czo3OiJyb2xlX2lkIjtpOjQ7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjE2OntzOjI6ImlkIjtpOjQ7czo0OiJuYW1lIjtzOjY6InN0YWZmMiI7czo1OiJlbWFpbCI7czoxNjoiYWhtYWRAYml6bWFyay5pZCI7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6ODoicGFzc3dvcmQiO3M6NjA6IiQyeSQxMiQ5bTRyRVc5eExOSWJzOU8zNHNGOXcuTVJROWRVTkRnNE9zdjNpY2RpZDdZMk95eTJwNC9lSyI7czoxNDoicmVtZW1iZXJfdG9rZW4iO047czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czo5OiJmdWxsX25hbWUiO3M6MTE6IkFobWFkIEZhZGxpIjtzOjg6InBvc2l0aW9uIjtzOjE2OiJLb25zdWx0YW4gSnVuaW9yIjtzOjU6InBob25lIjtzOjEyOiIwODEyMzQ1Njc4OTMiO3M6OToiaXNfYWN0aXZlIjtiOjE7czoxMzoibGFz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MyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNzoicHJvamVjdF9wZXJtaXRfaWQiO047czoxMjoiZGF5c19vdmVyZHVlIjtkOi01O31zOjExOiIAKgBvcmlnaW5hbCI7YToyMDp7czoyOiJpZCI7aToyO3M6MTA6InByb2plY3RfaWQiO2k6MTtzOjU6InRpdGxlIjtzOjI1OiJLb25zdWx0YXNpIGRlbmdhbiBBcnNpdGVrIjtzOjExOiJkZXNjcmlwdGlvbiI7czo2NjoiTWVsYWt1a2FuIGtvbnN1bHRhc2kgdGVrbmlzIGRlbmdhbiBhcnNpdGVrIHVudHVrIGZpbmFsaXNhc2kgZGVzYWluIjtzOjk6InNvcF9ub3RlcyI7TjtzOjE2OiJhc3NpZ25lZF91c2VyX2lkIjtpOjQ7czo4OiJkdWVfZGF0ZSI7czoxMDoiMjAyNS0xMS0wOCI7czoxMDoic3RhcnRlZF9hdCI7czoxOToiMjAyNS0xMS0wMSAxMjozMzoxNCI7czoxMjoiY29tcGxldGVkX2F0IjtOO3M6Njoic3RhdHVzIjtzOjExOiJpbl9wcm9ncmVzcyI7czo4OiJwcmlvcml0eSI7czo2OiJub3JtYWwiO3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtOO3M6MTg6ImRlcGVuZHNfb25fdGFza19pZCI7TjtzOjE2OiJjb21wbGV0aW9uX25vdGVzIjtOO3M6MTU6ImVzdGltYXRlZF9ob3VycyI7aTo4O3M6MTI6ImFjdHVhbF9ob3VycyI7TjtzOjEwOiJzb3J0X29yZGVyIjtpOjI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNzoicHJvamVjdF9wZXJtaXRfaWQiO047fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTE6IgAqAHByZXZpb3VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjM6e3M6ODoiZHVlX2RhdGUiO3M6NDoiZGF0ZSI7czoxMDoic3RhcnRlZF9hdCI7czo4OiJk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aWdoIjtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aToxO3M6MTg6ImRlcGVuZHNfb25fdGFza19pZCI7TjtzOjE2OiJjb21wbGV0aW9uX25vdGVzIjtOO3M6MTU6ImVzdGltYXRlZF9ob3VycyI7aToxNjtzOjEyOiJhY3R1YWxfaG91cnMiO047czoxMDoic29ydF9vcmRlciI7aToxO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTc6InByb2plY3RfcGVybWl0X2lkIjtOO3M6MTI6ImRheXNfb3ZlcmR1ZSI7ZDotMzt9czoxMToiACoAb3JpZ2luYWwiO2E6MjA6e3M6MjoiaWQiO2k6MTtzOjEwOiJwcm9qZWN0X2lkIjtpOjE7czo1OiJ0aXRsZSI7czoyOToiUGVyc2lhcGFuIERva3VtZW4gUGVyc3lhcmF0YW4iO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjc2OiJNZW5ndW1wdWxrYW4gZGFuIG1lbXBlcnNpYXBrYW4gc2VtdWEgZG9rdW1lbiBwZXJzeWFyYXRhbiB1bnR1ayBwZW5nYWp1YW4gSU1CIjtzOjk6InNvcF9ub3RlcyI7czoxMDY6IjEuIFN1cmF0IHBlcm1vaG9uYW4KMi4gS1RQIHBlbW9ob24KMy4gU3VyYXQga2VwZW1pbGlrYW4gdGFuYWgKNC4gR2FtYmFyIGFyc2l0ZWt0dXIKNS4gUGVyaGl0dW5nYW4gc3RydWt0dXIiO3M6MTY6ImFzc2lnbmVkX3VzZXJfaWQiO2k6MjtzOjg6ImR1ZV9kYXRlIjtzOjEwOiIyMDI1LTExLTEwIjtzOjEwOiJzdGFydGVkX2F0IjtOO3M6MTI6ImNvbXBsZXRlZF9hdCI7TjtzOjY6InN0YXR1cyI7czo0OiJ0b2RvIjtzOjg6InByaW9yaXR5IjtzOjQ6ImhpZ2giO3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjE7czoxODoiZGVwZW5kc19vbl90YXNrX2lkIjtOO3M6MTY6ImNvbXBsZXRpb25fbm90ZXMiO047czoxNToiZXN0aW1hdGVkX2hvdXJzIjtpOjE2O3M6MTI6ImFjdHVhbF9ob3VycyI7TjtzOjEwOiJzb3J0X29yZGVyIjtpOjE7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNzoicHJvamVjdF9wZXJtaXRfaWQiO047fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTE6IgAqAHByZXZpb3VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjM6e3M6ODoiZHVlX2RhdGUiO3M6NDoiZGF0ZSI7czoxMDoic3RhcnRlZF9hdCI7czo4OiJkYXRldGltZSI7czoxMjoiY29tcGxldGVkX2F0IjtzOjg6ImRhdGV0aW1lIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjI6e3M6NzoicHJvamVjdCI7cjozMjY7czoxMjoiYXNzaWduZWRVc2VyIjtPOjE1OiJBcHBcTW9kZWxzXFVzZXIiOjM1OntzOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6InBnc3FsIjtzOjg6IgAqAHRhYmxlIjtzOjU6InVzZXJzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MTY6e3M6MjoiaWQiO2k6MjtzOjQ6Im5hbWUiO3M6NzoibWFuYWdlciI7czo1OiJlbWFpbCI7czoxODoibWFuYWdlckBiaXptYXJrLmlkIjtzOjE3OiJlbWFpbF92ZXJpZmllZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7czo4OiJwYXNzd29yZCI7czo2MDoiJDJ5JDEyJFgyaGFmelpRTGZmRTNZTVdQZVZseGVMTXJMNGl0Z3JhTFk0WGYzQkFOQ3ZUVzdmVXUuOW1xIjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7TjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjk6ImZ1bGxfbmFtZSI7czoxMjoiQnVkaSBTYW50b3NvIjtzOjg6InBvc2l0aW9uIjtzOjE1OiJQcm9qZWN0IE1hbmFnZXIiO3M6NToicGhvbmUiO3M6MTI6IjA4MTIzNDU2Nzg5MSI7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEzOiJsYXN0X2xvZ2luX2F0IjtOO3M6NToibm90ZXMiO3M6MjQ6Ik1hbmFnZXIgcHJveWVrIHBlcml6aW5hbiI7czo2OiJhdmF0YXIiO047czo3OiJyb2xlX2lkIjtpOjE7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjE2OntzOjI6ImlkIjtpOjI7czo0OiJuYW1lIjtzOjc6Im1hbmFnZXIiO3M6NToiZW1haWwiO3M6MTg6Im1hbmFnZXJAYml6bWFyay5pZCI7czoxNzoiZW1haWxfdmVyaWZpZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6ODoicGFzc3dvcmQiO3M6NjA6IiQyeSQxMiRYMmhhZnpaUUxmZkUzWU1XUGVWbHhlTE1yTDRpdGdyYUxZNFhmM0JBTkN2VFc3ZlV1LjltcSI7czoxNDoicmVtZW1iZXJfdG9rZW4iO047czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAx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QW5hbGlzaXMga2FwYXNpdGFzIGphbGFuCjMuIFByZWRpa3NpIGRhbXBhawo0LiBSZW5jYW5hIG1pdGlnYXNpCjUuIFJla29tZW5kYXNpIjtzOjE2OiJhc3NpZ25lZF91c2VyX2lkIjtpOjI7czo4OiJkdWVfZGF0ZSI7czoxMDoiMjAyNS0xMS0xMyI7czoxMDoic3RhcnRlZF9hdCI7czoxOToiMjAyNS0xMC0zMSAxMjozMzoxNCI7czoxMjoiY29tcGxldGVkX2F0IjtOO3M6Njoic3RhdHVzIjtzOjExOiJpbl9wcm9ncmVzcyI7czo4OiJwcmlvcml0eSI7czo0OiJoaWdoIjtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7TjtzOjE4OiJkZXBlbmRzX29uX3Rhc2tfaWQiO047czoxNjoiY29tcGxldGlvbl9ub3RlcyI7TjtzOjE1OiJlc3RpbWF0ZWRfaG91cnMiO2k6MzI7czoxMjoiYWN0dWFsX2hvdXJzIjtpOjEyO3M6MTA6InNvcnRfb3JkZXIiO2k6MTtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjE3OiJwcm9qZWN0X3Blcm1pdF9pZCI7TjtzOjQ6InR5cGUiO3M6NDoidGFzayI7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjIwOntzOjI6ImlkIjtpOjU7czoxMDoicHJvamVjdF9pZCI7aTozO3M6NToidGl0bGUiO3M6MTc6IlN0dWRpIExhbHUgTGludGFzIjtzOjExOiJkZXNjcmlwdGlvbiI7czo2NjoiTWVsYWt1a2FuIHN0dWRpIGFuYWxpc2lzIGRhbXBhayBsYWx1IGxpbnRhcyB1bnR1ayBwZW1iYW5ndW5hbiBtYWxsIjtzOjk6InNvcF9ub3RlcyI7czoxMTA6IjEuIFN1cnZleSB2b2x1bWUgbGFsdSBsaW50YXMKMi4gQW5hbGlzaXMga2FwYXNpdGFzIGphbGFuCjMuIFByZWRpa3NpIGRhbXBhawo0LiBSZW5jYW5hIG1pdGlnYXNpCjUuIFJla29tZW5kYXNpIjtzOjE2OiJhc3NpZ25lZF91c2VyX2lkIjtpOjI7czo4OiJkdWVfZGF0ZSI7czoxMDoiMjAyNS0xMS0xMyI7czoxMDoic3RhcnRlZF9hdCI7czoxOToiMjAyNS0xMC0zMSAxMjozMzoxNCI7czoxMjoiY29tcGxldGVkX2F0IjtOO3M6Njoic3RhdHVzIjtzOjExOiJpbl9wcm9ncmVzcyI7czo4OiJwcmlvcml0eSI7czo0OiJoaWdoIjtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7TjtzOjE4OiJkZXBlbmRzX29uX3Rhc2tfaWQiO047czoxNjoiY29tcGxldGlvbl9ub3RlcyI7TjtzOjE1OiJlc3RpbWF0ZWRfaG91cnMiO2k6MzI7czoxMjoiYWN0dWFsX2hvdXJzIjtpOjEyO3M6MTA6InNvcnRfb3JkZXIiO2k6MTtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjE3OiJwcm9qZWN0X3Blcm1pdF9pZCI7Tjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxMToiACoAcHJldmlvdXMiO2E6MDp7fXM6ODoiACoAY2FzdHMiO2E6Mzp7czo4OiJkdWVfZGF0ZSI7czo0OiJkYXRlIjtzOjEwOiJzdGFydGVkX2F0IjtzOjg6ImRhdGV0aW1lIjtzOjEyOiJjb21wbGV0ZWRfYXQiO3M6ODoiZGF0ZXRpbWUiO31zOjE3OiIAKgBjbGFzc0Nhc3RDYWNoZSI7YTowOnt9czoyMToiACoAYXR0cmlidXRlQ2FzdENhY2hlIjthOjA6e31zOjEzOiIAKgBkYXRlRm9ybWF0IjtOO3M6MTA6IgAqAGFwcGVuZHMiO2E6MDp7fXM6MTk6IgAqAGRpc3BhdGNoZXNFdmVudHMiO2E6MDp7fXM6MTQ6IgAqAG9ic2VydmFibGVzIjthOjA6e31zOjEyOiIAKgByZWxhdGlvbnMiO2E6Mjp7czo3OiJwcm9qZWN0IjtPOjE4OiJBcHBcTW9kZWxzXFByb2plY3QiOjMzOntzOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6InBnc3FsIjtzOjg6IgAqAHRhYmxlIjtzOjg6InByb2plY3RzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MjQ6e3M6MjoiaWQiO2k6MztzOjQ6Im5hbWUiO3M6MzA6IkFuZGFsYWxpbiBNYWxsIFNob3BwaW5nIENlbnRlciI7czoxMToiZGVzY3JpcHRpb24iO3M6NzI6IkFuYWxpc2lzIERhbXBhayBMYWx1IExpbnRhcyB1bnR1ayBwZW1iYW5ndW5hbiBtYWxsIGRlbmdhbiBsdWFzIDE1LjAwMCBtMiI7czoxMToiY2xpZW50X25hbWUiO3M6MjQ6IlBUIE1ldHJvcG9saXRhbiBTaG9wcGluZyI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6Mjc6IkpsLiBSYXlhIEJvZ29yIEtNIDI1LCBEZXBvayI7czo5OiJzdGF0dXNfaWQiO2k6MTtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aTozO3M6NToibm90ZXMiO3M6ODQ6IlN1cnZleSB0cmFmZmljIGNvdW50aW5nIHN1ZGFoIGRpbXVsYWkuIEtvb3JkaW5hc2kgZGVuZ2FuIERpc2h1YiB1bnR1ayBkYXRhIHNla3VuZGVyLiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzg6IjAyMS05ODc2NTQzIC8gbWV0cm8uc2hvcHBpbmdAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTEwLTAxIjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI2LTAxLTMxIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjI1O3M6NjoiYnVkZ2V0IjtzOjEyOiIzMDAwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjExOiI3NTAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTE6IgAqAG9yaWdpbmFsIjthOjI0OntzOjI6ImlkIjtpOjM7czo0OiJuYW1lIjtzOjMwOiJBbmRhbGFsaW4gTWFsbCBTaG9wcGluZyBDZW50ZXIiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjcyOiJBbmFsaXNpcyBEYW1wYWsgTGFsdSBMaW50YXMgdW50dWsgcGVtYmFuZ3VuYW4gbWFsbCBkZW5nYW4gbHVhcyAxNS4wMDAgbTIiO3M6MTE6ImNsaWVudF9uYW1lIjtzOjI0OiJQVCBNZXRyb3BvbGl0YW4gU2hvcHBpbmciO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjI3OiJKbC4gUmF5YSBCb2dvciBLTSAyNSwgRGVwb2siO3M6OToic3RhdHVzX2lkIjtpOjE7czoxNDoiaW5zdGl0dXRpb25faWQiO2k6MztzOjU6Im5vdGVzIjtzOjg0OiJTdXJ2ZXkgdHJhZmZpYyBjb3VudGluZyBzdWRhaCBkaW11bGFpLiBLb29yZGluYXNpIGRlbmdhbiBEaXNodWIgdW50dWsgZGF0YSBzZWt1bmRlci4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjM4OiIwMjEtOTg3NjU0MyAvIG1ldHJvLnNob3BwaW5nQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0xMC0wMSI7czo4OiJkZWFkbGluZSI7czoxMDoiMjAyNi0wMS0zMSI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aToyNTtzOjY6ImJ1ZGdldCI7czoxMjoiMzAwMDAwMDAwLjAwIjtzOjExOiJhY3R1YWxfY29zdCI7czoxMToiNzUwMDAwMDAuMDAiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjQ6IjAuMDAiO3M6MTI6ImRvd25fcGF5bWVudCI7czo0OiIwLjAwIjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtzOjQ6IjAuMDAiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjQ6IjAuMDAiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6NDoiMC4wMCI7czoxMzoicGF5bWVudF90ZXJtcyI7TjtzOjE0OiJwYXltZW50X3N0YXR1cyI7czo2OiJ1bnBhaWQiO3M6OToiY2xpZW50X2lkIjtOO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjExOiIAKgBwcmV2aW91cyI7YTowOnt9czo4OiIAKgBjYXN0cyI7YToxMDp7czoxMDoic3RhcnRfZGF0ZSI7czo0OiJkYXRlIjtzOjg6ImRlYWRsaW5lIjtzOjQ6ImRhdGUiO3M6NjoiYnVkZ2V0IjtzOjk6ImRlY2ltYWw6MiI7czoxMToiYWN0dWFsX2Nvc3QiO3M6OToiZGVjaW1hbDoyIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtzOjc6ImludGVnZXIiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjk6ImRlY2ltYWw6MiI7czoxMjoiZG93bl9wYXltZW50IjtzOjk6ImRlY2ltYWw6MiI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo5OiJkZWNpbWFsOjIiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjk6ImRlY2ltYWw6MiI7czoxMzoi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MjoiMDgxMjM0NTY3ODkxIjtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6MTM6Imxhc3RfbG9naW5fYXQiO047czo1OiJub3RlcyI7czoyNDoiTWFuYWdlciBwcm95ZWsgcGVyaXppbmFuIjtzOjY6ImF2YXRhciI7TjtzOjc6InJvbGVfaWQiO2k6MTt9czoxMToiACoAb3JpZ2luYWwiO2E6MTY6e3M6MjoiaWQiO2k6MjtzOjQ6Im5hbWUiO3M6NzoibWFuYWdlciI7czo1OiJlbWFpbCI7czoxODoibWFuYWdlckBiaXptYXJrLmlkIjtzOjE3OiJlbWFpbF92ZXJpZmllZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7czo4OiJwYXNzd29yZCI7czo2MDoiJDJ5JDEyJFgyaGFmelpRTGZmRTNZTVdQZVZseGVMTXJMNGl0Z3JhTFk0WGYzQkFOQ3ZUVzdmVXUuOW1xIjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7TjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjk6ImZ1bGxfbmFtZSI7czoxMjoiQnVkaSBTYW50b3NvIjtzOjg6InBvc2l0aW9uIjtzOjE1OiJQcm9qZWN0IE1hbmFnZXIiO3M6NToicGhvbmUiO3M6MTI6IjA4MTIzNDU2Nzg5MSI7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEzOiJsYXN0X2xvZ2luX2F0IjtOO3M6NToibm90ZXMiO3M6MjQ6Ik1hbmFnZXIgcHJveWVrIHBlcml6aW5hbiI7czo2OiJhdmF0YXIiO047czo3OiJyb2xlX2lkIjtpOjE7fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTE6IgAqAHByZXZpb3VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjQ6e3M6MTc6ImVtYWlsX3ZlcmlmaWVkX2F0IjtzOjg6ImRhdGV0aW1lIjtzOjg6InBhc3N3b3JkIjtzOjY6Imhhc2hlZCI7czo5OiJpc19hY3RpdmUiO3M6NzoiYm9vbGVhbiI7czoxMzoibGFzdF9sb2dpbl9hdCI7czo4OiJkYXRldGltZSI7fXM6MTc6IgAqAGNsYXNzQ2FzdENhY2hlIjthOjA6e31zOjIxOiIAKgBhdHRyaWJ1dGVDYXN0Q2FjaGUiO2E6MDp7fXM6MTM6IgAqAGRhdGVGb3JtYXQiO047czoxMDoiACoAYXBwZW5kcyI7YTowOnt9czoxOToiACoAZGlzcGF0Y2hlc0V2ZW50cyI7YTowOnt9czoxNDoiACoAb2JzZXJ2YWJsZXMiO2E6MDp7fXM6MTI6IgAqAHJlbGF0aW9ucyI7YTowOnt9czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoyNzoiACoAcmVsYXRpb25BdXRvbG9hZENhbGxiYWNrIjtOO3M6MjY6IgAqAHJlbGF0aW9uQXV0b2xvYWRDb250ZXh0IjtOO3M6MTA6InRpbWVzdGFtcHMiO2I6MTtzOjEzOiJ1c2VzVW5pcXVlSWRzIjtiOjA7czo5OiIAKgBoaWRkZW4iO2E6Mjp7aTowO3M6ODoicGFzc3dvcmQiO2k6MTtzOjE0OiJyZW1lbWJlcl90b2tlbiI7fXM6MTA6IgAqAHZpc2libGUiO2E6MDp7fXM6MTE6IgAqAGZpbGxhYmxlIjthOjExOntpOjA7czo0OiJuYW1lIjtpOjE7czo5OiJmdWxsX25hbWUiO2k6MjtzOjU6ImVtYWlsIjtpOjM7czo4OiJwb3NpdGlvbiI7aTo0O3M6NToicGhvbmUiO2k6NTtzOjU6Im5vdGVzIjtpOjY7czo4OiJwYXNzd29yZCI7aTo3O3M6Nzoicm9sZV9pZCI7aTo4O3M6OToiaXNfYWN0aXZlIjtpOjk7czo2OiJhdmF0YXIiO2k6MTA7czoxMzoibGFzdF9sb2dpbl9hdCI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fXM6MTk6IgAqAGF1dGhQYXNzd29yZE5hbWUiO3M6ODoicGFzc3dvcmQiO3M6MjA6IgAqAHJlbWVtYmVyVG9rZW5OYW1lIjtzOjE0OiJyZW1lbWJlcl90b2tlbiI7fX1zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjI3OiIAKgByZWxhdGlvbkF1dG9sb2FkQ2FsbGJhY2siO047czoyNjoiACoAcmVsYXRpb25BdXRvbG9hZENvbnRleHQiO047czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTM6InVzZXNVbmlxdWVJZHMiO2I6MDtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6MTc6e2k6MDtzOjEwOiJwcm9qZWN0X2lkIjtpOjE7czo1OiJ0aXRsZSI7aToyO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjM7czo5OiJzb3Bfbm90ZXMiO2k6NDtzOjE2OiJhc3NpZ25lZF91c2VyX2lkIjtpOjU7czo2OiJzdGF0dXMiO2k6NjtzOjg6InByaW9yaXR5IjtpOjc7czo4OiJkdWVfZGF0ZSI7aTo4O3M6MTA6InN0YXJ0ZWRfYXQiO2k6OTtzOjEyOiJjb21wbGV0ZWRfYXQiO2k6MTA7czoxNjoiY29tcGxldGlvbl9ub3RlcyI7aToxMTtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aToxMjtzOjE4OiJkZXBlbmRzX29uX3Rhc2tfaWQiO2k6MTM7czoxNzoicHJvamVjdF9wZXJtaXRfaWQiO2k6MTQ7czoxNToiZXN0aW1hdGVkX2hvdXJzIjtpOjE1O3M6MTI6ImFjdHVhbF9ob3VycyI7aToxNjtzOjEwOiJzb3J0X29yZGVyIjt9czoxMDoiACoAZ3VhcmRlZCI7YToxOntpOjA7czoxOiIqIjt9fX1zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fXM6MTU6ImR1ZV90b2RheV9jb3VudCI7aToxO3M6MTI6InRvdGFsX3VyZ2VudCI7aTo1O3M6MTk6Imhhc19jcml0aWNhbF9hbGVydHMiO2I6MTt9czoxNDoiY2FzaEZsb3dTdGF0dXMiO2E6ODp7czoxMzoidG90YWxfYmFsYW5jZSI7aTowO3M6MTQ6ImF2YWlsYWJsZV9jYXNoIjtpOjA7czoxNToiY3VycmVudF9iYWxhbmNlIjtpOjA7czoxNzoibW9udGhseV9idXJuX3JhdGUiO2k6MDtzOjEzOiJydW53YXlfbW9udGhzIjtpOjA7czoxNjoib3ZlcmR1ZV9pbnZvaWNlcyI7aTowO3M6Njoic3RhdHVzIjtzOjc6InVua25vd24iO3M6MTI6InN0YXR1c19jb2xvciI7czo3OiIjOEU4RTkzIjt9czoxNjoicGVuZGluZ0FwcHJvdmFscyI7YTo1OntzOjE2OiJwZW5kaW5nX2ludm9pY2VzIjtPOjM5OiJJbGx1bWluYXRlXERhdGFiYXNlXEVsb3F1ZW50XENvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MDp7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9czoyMjoicGVuZGluZ19pbnZvaWNlc19jb3VudCI7aTowO3M6MTc6InBlbmRpbmdfZG9jdW1lbnRzIjtPOjM5OiJJbGx1bWluYXRlXERhdGFiYXNlXEVsb3F1ZW50XENvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MDp7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9czoyMzoicGVuZGluZ19kb2N1bWVudHNfY291bnQiO2k6MDtzOjEzOiJ0b3RhbF9wZW5kaW5nIjtpOjA7fXM6MTU6ImNhc2hGbG93U3VtbWFyeSI7YToxNzp7czoxODoidG90YWxfY2FzaF9iYWxhbmNlIjtpOjA7czoxNzoidGhpc19tb250aF9pbmNvbWUiO2k6MDtzOjE5OiJ0aGlzX21vbnRoX2V4cGVuc2VzIjtpOjA7czoxOToicGF5bWVudHNfdGhpc19tb250aCI7aTowO3M6MTk6ImV4cGVuc2VzX3RoaXNfbW9udGgiO2k6MDtzOjE0OiJuZXRfdGhpc19tb250aCI7aTowO3M6MTI6InBheW1lbnRzX3l0ZCI7aTowO3M6MTI6ImV4cGVuc2VzX3l0ZCI7aTowO3M6NzoibmV0X3l0ZCI7aTowO3M6MTk6InBheW1lbnRzX2xhc3RfbW9udGgiO2k6MDtzOjE5OiJleHBlbnNlc19sYXN0X21vbnRoIjtpOjA7czoxNDoibmV0X2xhc3RfbW9udGgiO2k6MDtzOjE1OiJwYXltZW50c19ncm93dGgiO2k6MDtzOjE1OiJleHBlbnNlc19ncm93dGgiO2k6MDtzOjEzOiJpc19wcm9maXRhYmxlIjtiOjA7czoxNDoidG90YWxfaW52b2ljZWQiO2k6MDtzOjE0OiJ0b3RhbF9yZWNlaXZlZCI7aTowO31zOjE2OiJyZWNlaXZhYmxlc0FnaW5nIjthOjg6e3M6NToiYWdpbmciO2E6NDp7czo3OiJjdXJyZW50IjtpOjA7czo1OiIzMV82MCI7aTowO3M6NToiNjFfOTAiO2k6MDtzOjc6Im92ZXJfOTAiO2k6MDt9czoxNzoidG90YWxfcmVjZWl2YWJsZXMiO2k6MDtzOjE5OiJpbnZvaWNlX3JlY2VpdmFibGVzIjtpOjA7czoyMDoiaW50ZXJuYWxfcmVjZWl2YWJsZXMiO2k6MDtzOjE0OiJpbnRlcm5hbF9hZ2luZyI7YTo0OntzOjc6ImN1cnJlbnQiO2k6MDtzOjU6IjMxXzYwIjtpOjA7czo1OiI2MV85MCI7aTowO3M6Nzoib3Zlcl85MCI7aTowO31zOjEzOiJpbnZvaWNlX2NvdW50IjtpOjA7czoxNDoiaW50ZXJuYWxfY291bnQiO2k6MDtzOjEzOiJpbnRlcm5hbF9saXN0IjtPOjM5OiJJbGx1bWluYXRlXERhdGFiYXNlXEVsb3F1ZW50XENvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MDp7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9fXM6MTI6ImJ1ZGdldFN0YXR1cyI7YTo0OntzOjEyOiJ0b3BfcHJvamVjdHMiO086Mzk6IklsbHVtaW5hdGVcRGF0YWJhc2VcRWxvcXVlbnRcQ29sbGVjdGlvbiI6Mjp7czo4OiIAKgBpdGVtcyI7YTowOnt9czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO31zOjEyOiJ0b3RhbF9idWRnZXQiO3M6MTM6IjEzMzAwMDAwMDAuMDAiO3M6MTE6InRvdGFsX3NwZW50IjtpOjA7czoxOToib3ZlcmFsbF91dGlsaXphdGlvbiI7ZDowO31zOjg6InRoaXNXZWVrIjthOjU6e3M6NToidGFza3MiO086Mjk6IklsbHVtaW5hdGVcU3VwcG9ydFxDb2xsZWN0aW9uIjoyOntzOjg6IgAqAGl0ZW1zIjthOjQ6e2k6MDthOjEyOntzOjI6ImlkIjtpOjU7czo1OiJ0aXRsZSI7czoxNzoiU3R1ZGkgTGFsdSBMaW50YXMiO3M6NzoicHJvamVjdCI7czozMDoiQW5kYWxhbGluIE1hbGwgU2hvcHBpbmcgQ2VudGVyIjtzOjEwOiJwcm9qZWN0X2lkIjtpOjM7czo4OiJkZWFkbGluZSI7TzoyNToiSWxsdW1pbmF0ZVxTdXBwb3J0XENhcmJvbiI6Mzp7czo0OiJkYXRlIjtzOjI2OiIyMDI1LTExLTEzIDAwOjAwOjAwLjAwMDAwMCI7czoxMzoidGltZXpvbmVfdHlwZSI7aTozO3M6ODoidGltZXpvbmUiO3M6MzoiVVRDIjt9czoxODoiZGVhZGxpbmVfZm9ybWF0dGVkIjtzOjExOiJUaHUsIDEzIE5vdiI7czoxMDoiZGF5c191bnRpbCI7ZDowLjQ2NDg2ODYwMjUyMzE0ODE2O3M6NzoiaXNfcGFzdCI7YjoxO3M6ODoiaXNfdG9kYXkiO2I6MTtzOjY6InN0YXR1cyI7czoxMToiaW5fcHJvZ3Jlc3MiO3M6MTE6ImFzc2lnbmVkX3RvIjtzOjc6Im1hbmFnZXIiO3M6MTQ6InByaW9yaXR5X2NvbG9yIjtzOjc6IiNGRjNCMzAiO31pOjE7YToxMjp7czoyOiJpZCI7aTozO3M6NToidGl0bGUiO3M6MjY6IkFuYWxpc2lzIERhbXBhayBMaW5na3VuZ2FuIjtzOjc6InByb2plY3QiO3M6MzI6IlBlcml6aW5hbiBVS0wtVVBMIFBhYnJpayBUZWtzdGlsIjtzOjEwOiJwcm9qZWN0X2lkIjtpOjI7czo4OiJkZWFkbGluZSI7TzoyNToiSWxsdW1pbmF0ZVxTdXBwb3J0XENhcmJvbiI6Mzp7czo0OiJkYXRlIjtzOjI2OiIyMDI1LTExLTE3IDAwOjAwOjAwLjAwMDAwMCI7czoxMzoidGltZXpvbmVfdHlwZSI7aTozO3M6ODoidGltZXpvbmUiO3M6MzoiVVRDIjt9czoxODoiZGVhZGxpbmVfZm9ybWF0dGVkIjtzOjExOiJNb24sIDE3IE5vdiI7czoxMDoiZGF5c191bnRpbCI7ZDozLjUzNTEzMTM5NTQxNjY2Njg7czo3OiJpc19wYXN0IjtiOjA7czo4OiJpc190b2RheSI7YjowO3M6Njoic3RhdHVzIjtzOjQ6InRvZG8iO3M6MTE6ImFzc2lnbmVkX3RvIjtzOjc6Im1hbmFnZXIiO3M6MTQ6InByaW9yaXR5X2NvbG9yIjtzOjc6IiMzNEM3NTkiO31pOjI7YToxMjp7czoyOiJpZCI7aTo2O3M6NToidGl0bGUiO3M6MjQ6Iktvb3JkaW5hc2kgZGVuZ2FuIERpc2h1YiI7czo3OiJwcm9qZWN0IjtzOjMwOiJBbmRhbGFsaW4gTWFsbCBTaG9wcGluZyBDZW50ZXIiO3M6MTA6InByb2plY3RfaWQiO2k6MztzOjg6ImRlYWRsaW5lIjtPOjI1OiJJbGx1bWluYXRlXFN1cHBvcnRcQ2FyYm9uIjozOntzOjQ6ImRhdGUiO3M6MjY6IjIwMjUtMTEtMTggMDA6MDA6MDAuMDAwMDAwIjtzOjEzOiJ0aW1lem9uZV90eXBlIjtpOjM7czo4OiJ0aW1lem9uZSI7czozOiJVVEMiO31zOjE4OiJkZWFkbGluZV9mb3JtYXR0ZWQiO3M6MTE6IlR1ZSwgMTggTm92IjtzOjEwOiJkYXlzX3VudGlsIjtkOjQuNTM1MTMxMzk0NTAyMzE1O3M6NzoiaXNfcGFzdCI7YjowO3M6ODoiaXNfdG9kYXkiO2I6MDtzOjY6InN0YXR1cyI7czo0OiJ0b2RvIjtzOjExOiJhc3NpZ25lZF90byI7czo1OiJoYWRleiI7czoxNDoicHJpb3JpdHlfY29sb3IiO3M6NzoiIzM0Qzc1OSI7fWk6MzthOjEyOntzOjI6ImlkIjtpOjQ7czo1OiJ0aXRsZSI7czoxNzoiUGVuZ2FqdWFuIGtlIERMSEsiO3M6NzoicHJvamVjdCI7czozMjoiUGVyaXppbmFuIFVLTC1VUEwgUGFicmlrIFRla3N0aWwiO3M6MTA6InByb2plY3RfaWQiO2k6MjtzOjg6ImRlYWRsaW5lIjtPOjI1OiJJbGx1bWluYXRlXFN1cHBvcnRcQ2FyYm9uIjozOntzOjQ6ImRhdGUiO3M6MjY6IjIwMjUtMTEtMjQgMDA6MDA6MDAuMDAwMDAwIjtzOjEzOiJ0aW1lem9uZV90eXBlIjtpOjM7czo4OiJ0aW1lem9uZSI7czozOiJVVEMiO31zOjE4OiJkZWFkbGluZV9mb3JtYXR0ZWQiO3M6MTE6Ik1vbiwgMjQg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UGVyaXppbmFuIElNQiBHZWR1bmcgUGVya2FudG9yYW4iO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjEwMzoiUGVuZ3VydXNhbiBJemluIE1lbmRpcmlrYW4gQmFuZ3VuYW4gdW50dWsgZ2VkdW5nIHBlcmthbnRvcmFuIDggbGFudGFpIGRpIGthd2FzYW4gYmlzbmlzIEpha2FydGEgU2VsYXRhbiI7czoxMToiY2xpZW50X25hbWUiO3M6MjU6IlBUIE1lZ2EgUHJpbWEgRGV2ZWxvcG1lbnQiO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjM3OiJKbC4gU3VkaXJtYW4gTm8uIDEyMywgSmFrYXJ0YSBTZWxhdGFuIjtzOjk6InN0YXR1c19pZCI7aToyO3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjE7czo1OiJub3RlcyI7czo3MzoiRG9rdW1lbiB0ZWtuaXMgc3VkYWggZGlzZXJhaGthbi4gTWVudW5nZ3UgdmVyaWZpa2FzaSBsYXBhbmdhbiBkYXJpIGRpbmFzLiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6MzQ6IjAyMS03NjU0MzIxIC8gbWVnYS5wcmltYUBlbWFpbC5jb20iO3M6MTA6InN0YXJ0X2RhdGUiO3M6MTA6IjIwMjUtMDktMTUiO3M6ODoiZGVhZGxpbmUiO3M6MTA6IjIwMjUtMTItMTUiO3M6MTk6InByb2dyZXNzX3BlcmNlbnRhZ2UiO2k6NjU7czo2OiJidWRnZXQiO3M6MTI6IjI1MDAwMDAwMC4wMCI7czoxMToiYWN0dWFsX2Nvc3QiO3M6MTI6IjE1MDAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTE6IgAqAG9yaWdpbmFsIjthOjI0OntzOjI6ImlkIjtpOjE7czo0OiJuYW1lIjtzOjMyOiJQZXJpemluYW4gSU1CIEdlZHVuZyBQZXJrYW50b3JhbiI7czoxMToiZGVzY3JpcHRpb24iO3M6MTAzOiJQZW5ndXJ1c2FuIEl6aW4gTWVuZGlyaWthbiBCYW5ndW5hbiB1bnR1ayBnZWR1bmcgcGVya2FudG9yYW4gOCBsYW50YWkgZGkga2F3YXNhbiBiaXNuaXMgSmFrYXJ0YSBTZWxhdGFuIjtzOjExOiJjbGllbnRfbmFtZSI7czoyNToiUFQgTWVnYSBQcmltYSBEZXZlbG9wbWVudCI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6Mzc6IkpsLiBTdWRpcm1hbiBOby4gMTIzLCBKYWthcnRhIFNlbGF0YW4iO3M6OToic3RhdHVzX2lkIjtpOjI7czoxNDoiaW5zdGl0dXRpb25faWQiO2k6MTtzOjU6Im5vdGVzIjtzOjczOiJEb2t1bWVuIHRla25pcyBzdWRhaCBkaXNlcmFoa2FuLiBNZW51bmdndSB2ZXJpZmlrYXNpIGxhcGFuZ2FuIGRhcmkgZGluYXMuIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjE0OiJjbGllbnRfY29udGFjdCI7czozNDoiMDIxLTc2NTQzMjEgLyBtZWdhLnByaW1hQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0wOS0xNSI7czo4OiJkZWFkbGluZSI7czoxMDoiMjAyNS0xMi0xNSI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aTo2NTtzOjY6ImJ1ZGdldCI7czoxMjoiMjUwMDAwMDAwLjAwIjtzOjExOiJhY3R1YWxfY29zdCI7czoxMjoiMTUwMDAwMDAwLjAwIjtzOjE0OiJjb250cmFjdF92YWx1ZSI7czo0OiIwLjAwIjtzOjEyOiJkb3duX3BheW1lbnQiO3M6NDoiMC4wMCI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo0OiIwLjAwIjtzOjE0OiJ0b3RhbF9leHBlbnNlcyI7czo0OiIwLjAwIjtzOjEzOiJwcm9maXRfbWFyZ2luIjtzOjQ6IjAuMDAiO3M6MTM6InBheW1lbnRfdGVybXMiO047czoxNDoicGF5bWVudF9zdGF0dXMiO3M6NjoidW5wYWlkIjtzOjk6ImNsaWVudF9pZCI7Tjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxMToiACoAcHJldmlvdXMiO2E6MDp7fXM6ODoiACoAY2FzdHMiO2E6MTA6e3M6MTA6InN0YXJ0X2RhdGUiO3M6NDoiZGF0ZSI7czo4OiJkZWFkbGluZSI7czo0OiJkYXRlIjtzOjY6ImJ1ZGdldCI7czo5OiJkZWNpbWFsOjIiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjk6ImRlY2ltYWw6MiI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7czo3OiJpbnRlZ2VyIjtzOjE0OiJjb250cmFjdF92YWx1ZSI7czo5OiJkZWNpbWFsOjIiO3M6MTI6ImRvd25fcGF5bWVudCI7czo5OiJkZWNpbWFsOjIiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6OToiZGVjaW1hbDoyIjtzOjE0OiJ0b3RhbF9leHBlbnNlcyI7czo5OiJkZWNpbWFsOjIiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6OToiZGVjaW1hbDoyIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjE6e3M6Njoic3RhdHVzIjtPOjI0OiJBcHBcTW9kZWxzXFByb2plY3RTdGF0dXMiOjMzOntzOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6InBnc3FsIjtzOjg6IgAqAHRhYmxlIjtzOjE2OiJwcm9qZWN0X3N0YXR1c2VzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MTA6e3M6MjoiaWQiO2k6MjtzOjQ6Im5hbWUiO3M6NzoiS29udHJhayI7czo0OiJjb2RlIjtzOjc6IktPTlRSQUsiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjI4OiJLb250cmFrIHRlbGFoIGRpdGFuZGF0YW5nYW5pIjtzOjU6ImNvbG9yIjtzOjc6IiMxMEI5ODEiO3M6MTA6InNvcnRfb3JkZXIiO2k6MjtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6ODoiaXNfZmluYWwiO2I6MDtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMToiACoAb3JpZ2luYWwiO2E6MTA6e3M6MjoiaWQiO2k6MjtzOjQ6Im5hbWUiO3M6NzoiS29udHJhayI7czo0OiJjb2RlIjtzOjc6IktPTlRSQUsiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjI4OiJLb250cmFrIHRlbGFoIGRpdGFuZGF0YW5nYW5pIjtzOjU6ImNvbG9yIjtzOjc6IiMxMEI5ODEiO3M6MTA6InNvcnRfb3JkZXIiO2k6MjtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6ODoiaXNfZmluYWwiO2I6MDtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxMToiACoAcHJldmlvdXMiO2E6MDp7fXM6ODoiACoAY2FzdHMiO2E6Mjp7czo5OiJpc19hY3RpdmUiO3M6NzoiYm9vbGVhbiI7czo4OiJpc19maW5hbCI7czo3OiJib29sZWFuIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjA6e31zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjI3OiIAKgByZWxhdGlvbkF1dG9sb2FkQ2FsbGJhY2siO047czoyNjoiACoAcmVsYXRpb25BdXRvbG9hZENvbnRleHQiO047czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTM6InVzZXNVbmlxdWVJZHMiO2I6MDtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6Nzp7aTowO3M6NDoibmFtZSI7aToxO3M6NDoiY29kZSI7aToyO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjM7czo1OiJjb2xvciI7aTo0O3M6MTA6InNvcnRfb3JkZXIiO2k6NTtzOjk6ImlzX2FjdGl2ZSI7aTo2O3M6ODoiaXNfZmluYWwiO31zOjEwOiIAKgBndWFyZGVkIjthOjE6e2k6MDtzOjE6IioiO319fXM6MTA6IgAqAHRvdWNoZXMiO2E6MDp7fXM6Mjc6IgAqAHJlbGF0aW9uQXV0b2xvYWRDYWxsYmFjayI7TjtzOjI2OiIAKgByZWxhdGlvbkF1dG9sb2FkQ29udGV4dCI7TjtzOjEwOiJ0aW1lc3RhbXBzIjtiOjE7czoxMzoidXNlc1VuaXF1ZUlkcyI7YjowO3M6OToiACoAaGlkZGVuIjthOjA6e31zOjEwOiIAKgB2aXNpYmxlIjthOjA6e31zOjExOiIAKgBmaWxsYWJsZSI7YToyMTp7aTowO3M6NDoibmFtZSI7aToxO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjI7czo5OiJjbGllbnRfaWQiO2k6MztzOjExOiJjbGllbnRfbmFtZSI7aTo0O3M6MTQ6ImNsaWVudF9jb250YWN0IjtpOjU7czoxNDoiY2xpZW50X2FkZHJlc3MiO2k6NjtzOjk6InN0YXR1c19pZCI7aTo3O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjg7czoxMDoic3RhcnRfZGF0ZSI7aTo5O3M6ODoiZGVhZGxpbmUiO2k6MTA7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aToxMTtzOjY6ImJ1ZGdldCI7aToxMjtzOjExOiJhY3R1YWxfY29zdCI7aToxMztzOjU6Im5vdGVzIjtpOjE0O3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtpOjE1O3M6MTI6ImRvd25fcGF5bWVudCI7aToxNjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtpOjE3O3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtpOjE4O3M6MTM6InByb2ZpdF9tYXJnaW4iO2k6MTk7czoxMzoicGF5bWVudF90ZXJtcyI7aToyMDtzOjE0OiJwYXltZW50X3N0YXR1cyI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX1pOjE7TzoxODoiQXBwXE1vZGVsc1xQcm9qZWN0IjozMzp7czoxMzoiACoAY29ubmVjdGlvbiI7czo1OiJwZ3NxbCI7czo4OiIAKgB0YWJsZSI7czo4OiJwcm9qZWN0cyI7czoxMzoiACoAcHJpbWFyeUtleSI7czoyOiJpZCI7czoxMDoiACoAa2V5VHlwZSI7czozOiJpbnQiO3M6MTI6ImluY3JlbWVudGluZyI7YjoxO3M6NzoiACoAd2l0aCI7YTowOnt9czoxMjoiACoAd2l0aENvdW50IjthOjA6e31zOjE5OiJwcmV2ZW50c0xhenlMb2FkaW5nIjtiOjA7czoxMDoiACoAcGVyUGFnZSI7aToxNTtzOjY6ImV4aXN0cyI7YjoxO3M6MTg6Indhc1JlY2VudGx5Q3JlYXRlZCI7YjowO3M6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDtzOjEzOiIAKgBhdHRyaWJ1dGVzIjthOjI0OntzOjI6ImlkIjtpOjU7czo0OiJuYW1lIjtzOjMxOiJQZXJpemluYW4gT1NTIFN0YXJ0dXAgVGVrbm9sb2dpIjtzOjExOiJkZXNjcmlwdGlvbiI7czo5MToiUGVuZ3VydXNhbiBpemluIHVzYWhhIG1lbGFsdWkgT25saW5lIFNpbmdsZSBTdWJtaXNzaW9uIHVudHVrIHBlcnVzYWhhYW4gdGVrbm9sb2dpIGZpbmFuc2lhbCI7czoxMToiY2xpZW50X25hbWUiO3M6Mjg6IlBUIERpZ2l0YWwgSW5vdmFzaSBJbmRvbmVzaWEiO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjQwOiJCU0QgR3JlZW4gT2ZmaWNlIFBhcmssIFRhbmdlcmFuZyBTZWxhdGFuIjtzOjk6InN0YXR1c19pZCI7aToyO3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjU7czo1OiJub3RlcyI7czo3MzoiQmVya2FzIE5JQiBzdWRhaCBkaXN1Ym1pdC4gTWVudW5nZ3UgcGVyc2V0dWp1YW4gZGFyaSBrZW1lbnRlcmlhbiB0ZXJrYWl0LiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzk6IjAyMS01NDMyMTY3IC8gZGlnaXRhbC5pbm92YXNpQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0wOS0yMCI7czo4OiJkZWFkbGluZSI7czoxMDoiMjAyNS0xMS0yMCI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aTo0MDtzOjY6ImJ1ZGdldCI7czoxMjoiMTIwMDAwMDAwLjAwIjtzOjExOiJhY3R1YWxfY29zdCI7czoxMToiNDgwMDAwMDAuMDAiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjQ6IjAuMDAiO3M6MTI6ImRvd25fcGF5bWVudCI7czo0OiIwLjAwIjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtzOjQ6IjAuMDAiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjQ6IjAuMDAiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6NDoiMC4wMCI7czoxMzoicGF5bWVudF90ZXJtcyI7TjtzOjE0OiJwYXltZW50X3N0YXR1cyI7czo2OiJ1bnBhaWQiO3M6OToiY2xpZW50X2lkIjtOO31zOjExOiIAKgBvcmlnaW5hbCI7YToyNDp7czoyOiJpZCI7aTo1O3M6NDoibmFtZSI7czozMToiUGVyaXppbmFuIE9TUyBTdGFydHVwIFRla25vbG9naSI7czoxMToiZGVzY3JpcHRpb24iO3M6OTE6IlBlbmd1cnVzYW4gaXppbiB1c2FoYSBtZWxhbHVpIE9ubGluZSBTaW5nbGUgU3VibWlzc2lvbiB1bnR1ayBwZXJ1c2FoYWFuIHRla25vbG9naSBmaW5hbnNpYWwiO3M6MTE6ImNsaWVudF9uYW1lIjtzOjI4OiJQVCBEaWdpdGFsIElub3Zhc2kgSW5kb25lc2lhIjtzOjE0OiJjbGllbnRfYWRkcmVzcyI7czo0MDoiQlNEIEdyZWVuIE9mZmljZSBQYXJrLCBUYW5nZXJhbmcgU2VsYXRhbiI7czo5OiJzdGF0dXNfaWQiO2k6MjtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aTo1O3M6NToibm90ZXMiO3M6NzM6IkJlcmthcyBOSUIgc3VkYWggZGlzdWJtaXQuIE1lbnVuZ2d1IHBlcnNldHVqdWFuIGRhcmkga2VtZW50ZXJpYW4gdGVya2FpdC4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjM5OiIwMjEtNTQzMjE2NyAvIGRpZ2l0YWwuaW5vdmFzaUBlbWFpbC5jb20iO3M6MTA6InN0YXJ0X2RhdGUiO3M6MTA6IjIwMjUtMDktMjAiO3M6ODoiZGVhZGxpbmUiO3M6MTA6IjIwMjUtMTEtMjAiO3M6MTk6InByb2dyZXNzX3BlcmNlbnRhZ2UiO2k6NDA7czo2OiJidWRnZXQiO3M6MTI6IjEyMDAwMDAwMC4wMCI7czoxMToiYWN0dWFsX2Nvc3QiO3M6MTE6IjQ4MDAwMDAwLjAwIjtzOjE0OiJjb250cmFjdF92YWx1ZSI7czo0OiIwLjAwIjtzOjEyOiJkb3duX3BheW1lbnQiO3M6NDoiMC4wMCI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo0OiIwLjAwIjtzOjE0OiJ0b3RhbF9leHBlbnNlcyI7czo0OiIwLjAwIjtzOjEzOiJwcm9maXRfbWFyZ2luIjtzOjQ6IjAuMDAiO3M6MTM6InBheW1lbnRfdGVybXMiO047czoxNDoicGF5bWVudF9zdGF0dXMiO3M6NjoidW5wYWlkIjtzOjk6ImNsaWVudF9pZCI7Tjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxMToiACoAcHJldmlvdXMiO2E6MDp7fXM6ODoiACoAY2FzdHMiO2E6MTA6e3M6MTA6InN0YXJ0X2RhdGUiO3M6NDoiZGF0ZSI7czo4OiJkZWFkbGluZSI7czo0OiJkYXRlIjtzOjY6ImJ1ZGdldCI7czo5OiJkZWNpbWFsOjIiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjk6ImRlY2ltYWw6MiI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7czo3OiJpbnRlZ2VyIjtzOjE0OiJjb250cmFjdF92YWx1ZSI7czo5OiJkZWNpbWFsOjIiO3M6MTI6ImRvd25fcGF5bWVudCI7czo5OiJkZWNpbWFsOjIiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6OToiZGVjaW1hbDoyIjtzOjE0OiJ0b3RhbF9leHBlbnNlcyI7czo5OiJkZWNpbWFsOjIiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6OToiZGVjaW1hbDoyIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjE6e3M6Njoic3RhdHVzIjtyOjE1MTc7fXM6MTA6IgAqAHRvdWNoZXMiO2E6MDp7fXM6Mjc6IgAqAHJlbGF0aW9uQXV0b2xvYWRDYWxsYmFjayI7TjtzOjI2OiIAKgByZWxhdGlvbkF1dG9sb2FkQ29udGV4dCI7TjtzOjEwOiJ0aW1lc3RhbXBzIjtiOjE7czoxMzoidXNlc1VuaXF1ZUlkcyI7YjowO3M6OToiACoAaGlkZGVuIjthOjA6e31zOjEwOiIAKgB2aXNpYmxlIjthOjA6e31zOjExOiIAKgBmaWxsYWJsZSI7YToyMTp7aTowO3M6NDoibmFtZSI7aToxO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjI7czo5OiJjbGllbnRfaWQiO2k6MztzOjExOiJjbGllbnRfbmFtZSI7aTo0O3M6MTQ6ImNsaWVudF9jb250YWN0IjtpOjU7czoxNDoiY2xpZW50X2FkZHJlc3MiO2k6NjtzOjk6InN0YXR1c19pZCI7aTo3O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjg7czoxMDoic3RhcnRfZGF0ZSI7aTo5O3M6ODoiZGVhZGxpbmUiO2k6MTA7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aToxMTtzOjY6ImJ1ZGdldCI7aToxMjtzOjExOiJhY3R1YWxfY29zdCI7aToxMztzOjU6Im5vdGVzIjtpOjE0O3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtpOjE1O3M6MTI6ImRvd25fcGF5bWVudCI7aToxNjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtpOjE3O3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtpOjE4O3M6MTM6InByb2ZpdF9tYXJnaW4iO2k6MTk7czoxMzoicGF5bWVudF90ZXJtcyI7aToyMDtzOjE0OiJwYXltZW50X3N0YXR1cyI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX19czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO319czoxOToiUGVuZ3VtcHVsYW4gRG9rdW1lbiI7YTo0OntzOjExOiJzdGF0dXNfbmFtZSI7czoxOToiUGVuZ3VtcHVsYW4gRG9rdW1lbiI7czo1OiJjb3VudCI7aToxO3M6NToiY29sb3IiO3M6NzoiI0Y1OUUwQiI7czo4OiJwcm9qZWN0cyI7TzozOToiSWxsdW1pbmF0ZVxEYXRhYmFzZVxFbG9xdWVudFxDb2xsZWN0aW9uIjoyOntzOjg6IgAqAGl0ZW1zIjthOjE6e2k6MDtPOjE4OiJBcHBcTW9kZWxzXFByb2plY3QiOjMzOntzOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6InBnc3FsIjtzOjg6IgAqAHRhYmxlIjtzOjg6InByb2plY3RzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MjQ6e3M6MjoiaWQiO2k6MjtzOjQ6Im5hbWUiO3M6MzI6IlBlcml6aW5hbiBVS0wtVVBMIFBhYnJpayBUZWtzdGlsIjtzOjExOiJkZXNjcmlwdGlvbiI7czoxMDA6IlBlbmd1cnVzYW4gcGVyaXppbmFuIGxpbmdrdW5nYW4gVUtMLVVQTCB1bnR1ayBwYWJyaWsgdGVrc3RpbCBkZW5nYW4ga2FwYXNpdGFzIHByb2R1a3NpIDUwMCB0b24vYnVsYW4iO3M6MTE6ImNsaWVudF9uYW1lIjtzOjIwOiJDViBUZWtzdGlsIE51c2FudGFyYSI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6MzM6Ikthd2FzYW4gSW5kdXN0cmkgQ2liaXR1bmcsIEJla2FzaSI7czo5OiJzdGF0dXNfaWQiO2k6MztzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aToyO3M6NToibm90ZXMiO3M6Njc6IkRva3VtZW4gVUtMLVVQTCB0ZWxhaCBkaXNlcmFoa2FuLiBTZWRhbmcgZGFsYW0gdGFoYXAgcmV2aWV3IHRla25pcy4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjQyOiIwMjUxLTg3NjU0MzIgLyB0ZWtzdGlsLm51c2FudGFyYUBlbWFpbC5jb20iO3M6MTA6InN0YXJ0X2RhdGUiO3M6MTA6IjIwMjUtMDgtMDEiO3M6ODoiZGVhZGxpbmUiO3M6MTA6IjIwMjUtMTEtMzAiO3M6MTk6InByb2dyZXNzX3BlcmNlbnRhZ2UiO2k6ODA7czo2OiJidWRnZXQiO3M6MTI6IjE3NTAwMDAwMC4wMCI7czoxMToiYWN0dWFsX2Nvc3QiO3M6MTI6IjE0MDAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTE6IgAqAG9yaWdpbmFsIjthOjI0OntzOjI6ImlkIjtpOjI7czo0OiJuYW1lIjtzOjMyOiJQZXJpemluYW4gVUtMLVVQTCBQYWJyaWsgVGVrc3RpbCI7czoxMToiZGVzY3JpcHRpb24iO3M6MTAwOiJQZW5ndXJ1c2FuIHBlcml6aW5hbiBsaW5na3VuZ2FuIFVLTC1VUEwgdW50dWsgcGFicmlrIHRla3N0aWwgZGVuZ2FuIGthcGFzaXRhcyBwcm9kdWtzaSA1MDAgdG9uL2J1bGFuIjtzOjExOiJjbGllbnRfbmFtZSI7czoyMDoiQ1YgVGVrc3RpbCBOdXNhbnRhcmEiO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjMzOiJLYXdhc2FuIEluZHVzdHJpIENpYml0dW5nLCBCZWthc2kiO3M6OToic3RhdHVzX2lkIjtpOjM7czoxNDoiaW5zdGl0dXRpb25faWQiO2k6MjtzOjU6Im5vdGVzIjtzOjY3OiJEb2t1bWVuIFVLTC1VUEwgdGVsYWggZGlzZXJhaGthbi4gU2VkYW5nIGRhbGFtIHRhaGFwIHJldmlldyB0ZWtuaXMuIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjE0IjtzOjE0OiJjbGllbnRfY29udGFjdCI7czo0MjoiMDI1MS04NzY1NDMyIC8gdGVrc3RpbC5udXNhbnRhcmFAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTA4LTAxIjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI1LTExLTMwIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjgwO3M6NjoiYnVkZ2V0IjtzOjEyOiIxNzUwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjEyOiIxNDAwMDAwMDAuMDAiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjQ6IjAuMDAiO3M6MTI6ImRvd25fcGF5bWVudCI7czo0OiIwLjAwIjtzOjE2OiJwYXltZW50X3JlY2VpdmVkIjtzOjQ6IjAuMDAiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjQ6IjAuMDAiO3M6MTM6InByb2ZpdF9tYXJnaW4iO3M6NDoiMC4wMCI7czoxMzoicGF5bWVudF90ZXJtcyI7TjtzOjE0OiJwYXltZW50X3N0YXR1cyI7czo2OiJ1bnBhaWQiO3M6OToiY2xpZW50X2lkIjtOO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjExOiIAKgBwcmV2aW91cyI7YTowOnt9czo4OiIAKgBjYXN0cyI7YToxMDp7czoxMDoic3RhcnRfZGF0ZSI7czo0OiJkYXRlIjtzOjg6ImRlYWRsaW5lIjtzOjQ6ImRhdGUiO3M6NjoiYnVkZ2V0IjtzOjk6ImRlY2ltYWw6MiI7czoxMToiYWN0dWFsX2Nvc3QiO3M6OToiZGVjaW1hbDoyIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtzOjc6ImludGVnZXIiO3M6MTQ6ImNvbnRyYWN0X3ZhbHVlIjtzOjk6ImRlY2ltYWw6MiI7czoxMjoiZG93bl9wYXltZW50IjtzOjk6ImRlY2ltYWw6MiI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo5OiJkZWNpbWFsOjIiO3M6MTQ6InRvdGFsX2V4cGVuc2VzIjtzOjk6ImRlY2ltYWw6MiI7czoxMzoicHJvZml0X21hcmdpbiI7czo5OiJkZWNpbWFsOjIiO31zOjE3OiIAKgBjbGFzc0Nhc3RDYWNoZSI7YTowOnt9czoyMToiACoAYXR0cmlidXRlQ2FzdENhY2hlIjthOjA6e31zOjEzOiIAKgBkYXRlRm9ybWF0IjtOO3M6MTA6IgAqAGFwcGVuZHMiO2E6MDp7fXM6MTk6IgAqAGRpc3BhdGNoZXNFdmVudHMiO2E6MDp7fXM6MTQ6IgAqAG9ic2VydmFibGVzIjthOjA6e31zOjEyOiIAKgByZWxhdGlvbnMiO2E6MTp7czo2OiJzdGF0dXMiO086MjQ6IkFwcFxNb2RlbHNcUHJvamVjdFN0YXR1cyI6MzM6e3M6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToicGdzcWwiO3M6ODoiACoAdGFibGUiO3M6MTY6InByb2plY3Rfc3RhdHVzZXMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YToxMDp7czoyOiJpZCI7aTozO3M6NDoibmFtZSI7czoxOToiUGVuZ3VtcHVsYW4gRG9rdW1lbiI7czo0OiJjb2RlIjtzOjE1OiJQRU5HVU1QVUxBTl9ET0siO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjM0OiJQZW5ndW1wdWxhbiBkYW4gcGVueXVzdW5hbiBkb2t1bWVuIjtzOjU6ImNvbG9yIjtzOjc6IiNGNTlFMEIiO3M6MTA6InNvcnRfb3JkZXIiO2k6MztzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6ODoiaXNfZmluYWwiO2I6MDtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMToiACoAb3JpZ2luYWwiO2E6MTA6e3M6MjoiaWQiO2k6MztzOjQ6Im5hbWUiO3M6MTk6IlBlbmd1bXB1bGFuIERva3VtZW4iO3M6NDoiY29kZSI7czoxNToiUEVOR1VNUFVMQU5fRE9LIjtzOjExOiJkZXNjcmlwdGlvbiI7czozNDoiUGVuZ3VtcHVsYW4gZGFuIHBlbnl1c3VuYW4gZG9rdW1lbiI7czo1OiJjb2xvciI7czo3OiIjRjU5RTBCIjtzOjEwOiJzb3J0X29yZGVyIjtpOjM7czo5OiJpc19hY3RpdmUiO2I6MTtzOjg6ImlzX2ZpbmFsIjtiOjA7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTE6IgAqAHByZXZpb3VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjI6e3M6OToiaXNfYWN0aXZlIjtzOjc6ImJvb2xlYW4iO3M6ODoiaXNfZmluYWwiO3M6NzoiYm9vbGVhbiI7fXM6MTc6IgAqAGNsYXNzQ2FzdENhY2hlIjthOjA6e31zOjIxOiIAKgBhdHRyaWJ1dGVDYXN0Q2FjaGUiO2E6MDp7fXM6MTM6IgAqAGRhdGVGb3JtYXQiO047czoxMDoiACoAYXBwZW5kcyI7YTowOnt9czoxOToiACoAZGlzcGF0Y2hlc0V2ZW50cyI7YTowOnt9czoxNDoiACoAb2JzZXJ2YWJsZXMiO2E6MDp7fXM6MTI6IgAqAHJlbGF0aW9ucyI7YTowOnt9czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoyNzoiACoAcmVsYXRpb25BdXRvbG9hZENhbGxiYWNrIjtOO3M6MjY6IgAqAHJlbGF0aW9uQXV0b2xvYWRDb250ZXh0IjtOO3M6MTA6InRpbWVzdGFtcHMiO2I6MTtzOjEzOiJ1c2VzVW5pcXVlSWRzIjtiOjA7czo5OiIAKgBoaWRkZW4iO2E6MDp7fXM6MTA6IgAqAHZpc2libGUiO2E6MDp7fXM6MTE6IgAqAGZpbGxhYmxlIjthOjc6e2k6MDtzOjQ6Im5hbWUiO2k6MTtzOjQ6ImNvZGUiO2k6MjtzOjExOiJkZXNjcmlwdGlvbiI7aTozO3M6NToiY29sb3IiO2k6NDtzOjEwOiJzb3J0X29yZGVyIjtpOjU7czo5OiJpc19hY3RpdmUiO2k6NjtzOjg6ImlzX2ZpbmFsIjt9czoxMDoiACoAZ3VhcmRlZCI7YToxOntpOjA7czoxOiIqIjt9fX1zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjI3OiIAKgByZWxhdGlvbkF1dG9sb2FkQ2FsbGJhY2siO047czoyNjoiACoAcmVsYXRpb25BdXRvbG9hZENvbnRleHQiO047czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTM6InVzZXNVbmlxdWVJZHMiO2I6MDtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6MjE6e2k6MDtzOjQ6Im5hbWUiO2k6MTtzOjExOiJkZXNjcmlwdGlvbiI7aToyO3M6OToiY2xpZW50X2lkIjtpOjM7czoxMToiY2xpZW50X25hbWUiO2k6NDtzOjE0OiJjbGllbnRfY29udGFjdCI7aTo1O3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtpOjY7czo5OiJzdGF0dXNfaWQiO2k6NztzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aTo4O3M6MTA6InN0YXJ0X2RhdGUiO2k6OTtzOjg6ImRlYWRsaW5lIjtpOjEwO3M6MTk6InByb2dyZXNzX3BlcmNlbnRhZ2UiO2k6MTE7czo2OiJidWRnZXQiO2k6MTI7czoxMToiYWN0dWFsX2Nvc3QiO2k6MTM7czo1OiJub3RlcyI7aToxNDtzOjE0OiJjb250cmFjdF92YWx1ZSI7aToxNTtzOjEyOiJkb3duX3BheW1lbnQiO2k6MTY7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7aToxNztzOjE0OiJ0b3RhbF9leHBlbnNlcyI7aToxODtzOjEzOiJwcm9maXRfbWFyZ2luIjtpOjE5O3M6MTM6InBheW1lbnRfdGVybXMiO2k6MjA7czoxNDoicGF5bWVudF9zdGF0dXMiO31zOjEwOiIAKgBndWFyZGVkIjthOjE6e2k6MDtzOjE6IioiO319fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9fXM6OToiUGVuYXdhcmFuIjthOjQ6e3M6MTE6InN0YXR1c19uYW1lIjtzOjk6IlBlbmF3YXJhbiI7czo1OiJjb3VudCI7aToxO3M6NToiY29sb3IiO3M6NzoiIzNCODJGNiI7czo4OiJwcm9qZWN0cyI7TzozOToiSWxsdW1pbmF0ZVxEYXRhYmFzZVxFbG9xdWVudFxDb2xsZWN0aW9uIjoyOntzOjg6IgAqAGl0ZW1zIjthOjE6e2k6MDtPOjE4OiJBcHBcTW9kZWxzXFByb2plY3QiOjMzOntzOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6InBnc3FsIjtzOjg6IgAqAHRhYmxlIjtzOjg6InByb2plY3RzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MjQ6e3M6MjoiaWQiO2k6MztzOjQ6Im5hbWUiO3M6MzA6IkFuZGFsYWxpbiBNYWxsIFNob3BwaW5nIENlbnRlciI7czoxMToiZGVzY3JpcHRpb24iO3M6NzI6IkFuYWxpc2lzIERhbXBhayBMYWx1IExpbnRhcyB1bnR1ayBwZW1iYW5ndW5hbiBtYWxsIGRlbmdhbiBsdWFzIDE1LjAwMCBtMiI7czoxMToiY2xpZW50X25hbWUiO3M6MjQ6IlBUIE1ldHJvcG9saXRhbiBTaG9wcGluZyI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6Mjc6IkpsLiBSYXlhIEJvZ29yIEtNIDI1LCBEZXBvayI7czo5OiJzdGF0dXNfaWQiO2k6MTtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aTozO3M6NToibm90ZXMiO3M6ODQ6IlN1cnZleSB0cmFmZmljIGNvdW50aW5nIHN1ZGFoIGRpbXVsYWkuIEtvb3JkaW5hc2kgZGVuZ2FuIERpc2h1YiB1bnR1ayBkYXRhIHNla3VuZGVyLiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzg6IjAyMS05ODc2NTQzIC8gbWV0cm8uc2hvcHBpbmdAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTEwLTAxIjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI2LTAxLTMxIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjI1O3M6NjoiYnVkZ2V0IjtzOjEyOiIzMDAwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjExOiI3NTAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTE6IgAqAG9yaWdpbmFsIjthOjI0OntzOjI6ImlkIjtpOjM7czo0OiJuYW1lIjtzOjMwOiJBbmRhbGFsaW4gTWFsbCBTaG9wcGluZyBDZW50ZXIiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjcyOiJBbmFsaXNpcyBEYW1wYWsgTGFsdSBMaW50YXMgdW50dWsgcGVtYmFuZ3VuYW4gbWFsbCBkZW5nYW4gbHVhcyAxNS4wMDAgbTIiO3M6MTE6ImNsaWVudF9uYW1lIjtzOjI0OiJQVCBNZXRyb3BvbGl0YW4gU2hvcHBpbmciO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjI3OiJKbC4gUmF5YSBCb2dvciBLTSAyNSwgRGVwb2siO3M6OToic3RhdHVzX2lkIjtpOjE7czoxNDoiaW5zdGl0dXRpb25faWQiO2k6MztzOjU6Im5vdGVzIjtzOjg0OiJTdXJ2ZXkgdHJhZmZpYyBjb3VudGluZyBzdWRhaCBkaW11bGFpLiBLb29yZGluYXNpIGRlbmdhbiBEaXNodWIgdW50dWsgZGF0YSBzZWt1bmRlci4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjM4OiIwMjEtOTg3NjU0MyAvIG1ldHJvLnNob3BwaW5nQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0xMC0w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aSBETEgiO3M6NToiY291bnQiO2k6MTtzOjU6ImNvbG9yIjtzOjc6IiM4QjVDRjYiO3M6ODoicHJvamVjdHMiO086Mzk6IklsbHVtaW5hdGVcRGF0YWJhc2VcRWxvcXVlbnRcQ29sbGVjdGlvbiI6Mjp7czo4OiIAKgBpdGVtcyI7YToxOntpOjA7TzoxODoiQXBwXE1vZGVsc1xQcm9qZWN0IjozMzp7czoxMzoiACoAY29ubmVjdGlvbiI7czo1OiJwZ3NxbCI7czo4OiIAKgB0YWJsZSI7czo4OiJwcm9qZWN0cyI7czoxMzoiACoAcHJpbWFyeUtleSI7czoyOiJpZCI7czoxMDoiACoAa2V5VHlwZSI7czozOiJpbnQiO3M6MTI6ImluY3JlbWVudGluZyI7YjoxO3M6NzoiACoAd2l0aCI7YTowOnt9czoxMjoiACoAd2l0aENvdW50IjthOjA6e31zOjE5OiJwcmV2ZW50c0xhenlMb2FkaW5nIjtiOjA7czoxMDoiACoAcGVyUGFnZSI7aToxNTtzOjY6ImV4aXN0cyI7YjoxO3M6MTg6Indhc1JlY2VudGx5Q3JlYXRlZCI7YjowO3M6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDtzOjEzOiIAKgBhdHRyaWJ1dGVzIjthOjI0OntzOjI6ImlkIjtpOjQ7czo0OiJuYW1lIjtzOjMxOiJTZXJ0aWZpa2F0IEhhbGFsIFByb2R1ayBNYWthbmFuIjtzOjExOiJkZXNjcmlwdGlvbiI7czo2NToiUGVuZ3VydXNhbiBzZXJ0aWZpa2F0IGhhbGFsIHVudHVrIDE1IHZhcmlhbiBwcm9kdWsgbWFrYW5hbiBvbGFoYW4iO3M6MTE6ImNsaWVudF9uYW1lIjtzOjE3OiJVRCBCZXJrYWggTWFuZGlyaSI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6MzI6IkpsLiBNYWxpb2Jvcm8gTm8uIDQ1LCBZb2d5YWthcnRhIjtzOjk6InN0YXR1c19pZCI7aTo0O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjQ7czo1OiJub3RlcyI7czo4MjoiU2VydGlmaWthdCBoYWxhbCB0ZWxhaCBkaXRlcmJpdGthbiB1bnR1ayBzZW11YSBwcm9kdWsuIFByb3NlcyBzZWxlc2FpIHRlcGF0IHdha3R1LiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzg6IjAyNzQtNjU0MzIxIC8gYmVya2FoLm1hbmRpcmlAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTA3LTAxIjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI1LTA5LTMwIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjEwMDtzOjY6ImJ1ZGdldCI7czoxMToiODUwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjExOiI4MDAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTE6IgAqAG9yaWdpbmFsIjthOjI0OntzOjI6ImlkIjtpOjQ7czo0OiJuYW1lIjtzOjMxOiJTZXJ0aWZpa2F0IEhhbGFsIFByb2R1ayBNYWthbmFuIjtzOjExOiJkZXNjcmlwdGlvbiI7czo2NToiUGVuZ3VydXNhbiBzZXJ0aWZpa2F0IGhhbGFsIHVudHVrIDE1IHZhcmlhbiBwcm9kdWsgbWFrYW5hbiBvbGFoYW4iO3M6MTE6ImNsaWVudF9uYW1lIjtzOjE3OiJVRCBCZXJrYWggTWFuZGlyaSI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6MzI6IkpsLiBNYWxpb2Jvcm8gTm8uIDQ1LCBZb2d5YWthcnRhIjtzOjk6InN0YXR1c19pZCI7aTo0O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjQ7czo1OiJub3RlcyI7czo4MjoiU2VydGlmaWthdCBoYWxhbCB0ZWxhaCBkaXRlcmJpdGthbiB1bnR1ayBzZW11YSBwcm9kdWsuIFByb3NlcyBzZWxlc2FpIHRlcGF0IHdha3R1LiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzg6IjAyNzQtNjU0MzIxIC8gYmVya2FoLm1hbmRpcmlAZW1haWwuY29tIjtzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI1LTA3LTAxIjtzOjg6ImRlYWRsaW5lIjtzOjEwOiIyMDI1LTA5LTMwIjtzOjE5OiJwcm9ncmVzc19wZXJjZW50YWdlIjtpOjEwMDtzOjY6ImJ1ZGdldCI7czoxMToiODUwMDAwMDAuMDAiO3M6MTE6ImFjdHVhbF9jb3N0IjtzOjExOiI4MDAwMDAwMC4wMCI7czoxNDoiY29udHJhY3RfdmFsdWUiO3M6NDoiMC4wMCI7czoxMjoiZG93bl9wYXltZW50IjtzOjQ6IjAuMDAiO3M6MTY6InBheW1lbnRfcmVjZWl2ZWQiO3M6NDoiMC4wMCI7czoxNDoidG90YWxfZXhwZW5zZXMiO3M6NDoiMC4wMCI7czoxMzoicHJvZml0X21hcmdpbiI7czo0OiIwLjAwIjtzOjEzOiJwYXltZW50X3Rlcm1zIjtOO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjY6InVucGFpZCI7czo5OiJjbGllbnRfaWQiO047fXM6MTA6IgAqAGNoYW5nZXMiO2E6MDp7fXM6MTE6IgAqAHByZXZpb3VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjEwOntzOjEwOiJzdGFydF9kYXRlIjtzOjQ6ImRhdGUiO3M6ODoiZGVhZGxpbmUiO3M6NDoiZGF0ZSI7czo2OiJidWRnZXQiO3M6OToiZGVjaW1hbDoyIjtzOjExOiJhY3R1YWxfY29zdCI7czo5OiJkZWNpbWFsOjIi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ACoAYXR0cmlidXRlcyI7YToyNDp7czoyOiJpZCI7aTo2O3M6NDoibmFtZSI7czoyMToiS0tQUiBLYXdhc2FuIEluZHVzdHJpIjtzOjExOiJkZXNjcmlwdGlvbiI7czo5NDoiS2FqaWFuIEtlc2VsYW1hdGFuIGRhbiBLZXNlaGF0YW4gS2VyamEgc2VydGEgUGVybGluZHVuZ2FuIFJhZGlhc2kgdW50dWsga2F3YXNhbiBpbmR1c3RyaSBraW1pYSI7czoxMToiY2xpZW50X25hbWUiO3M6Mjc6IlBUIEluZHVzdHJpIEtpbWlhIFNlamFodGVyYSI7czoxNDoiY2xpZW50X2FkZHJlc3MiO3M6MzI6Ikthd2FzYW4gSW5kdXN0cmkgQ2lsZWdvbiwgQmFudGVuIjtzOjk6InN0YXR1c19pZCI7aTo1O3M6MTQ6Imluc3RpdHV0aW9uX2lkIjtpOjE7czo1OiJub3RlcyI7czo3OToiUHJvc2VzIGRpdHVuZGEga2FyZW5hIG1lbnVuZ2d1IGhhc2lsIGF1ZGl0IHNhZmV0eSBkYXJpIGtvbnN1bHRhbiBpbnRlcm5hc2lvbmFsLiI7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxNCI7czoxNDoiY2xpZW50X2NvbnRhY3QiO3M6Mzk6IjAyMS03ODkxMjM0IC8ga2ltaWEuc2VqYWh0ZXJhQGVtYWlsLmNvbSI7czoxMDoic3RhcnRfZGF0ZSI7czoxMDoiMjAyNS0wOC0xNSI7czo4OiJkZWFkbGluZSI7czoxMDoiMjAyNS0xMi0zMSI7czoxOToicHJvZ3Jlc3NfcGVyY2VudGFnZSI7aTozMDtzOjY6ImJ1ZGdldCI7czoxMjoiNDAwMDAwMDAwLjAwIjtzOjExOiJhY3R1YWxfY29zdCI7czoxMjoiMTIwMDAwMDAwLjAwIjtzOjE0OiJjb250cmFjdF92YWx1ZSI7czo0OiIwLjAwIjtzOjEyOiJkb3duX3BheW1lbnQiO3M6NDoiMC4wMCI7czoxNjoicGF5bWVudF9yZWNlaXZlZCI7czo0OiIwLjAwIjtzOjE0OiJ0b3RhbF9leHBlbnNlcyI7czo0OiIwLjAwIjtzOjEzOiJwcm9maXRfbWFyZ2luIjtzOjQ6IjAuMDAiO3M6MTM6InBheW1lbnRfdGVybXMiO047czoxNDoicGF5bWVudF9zdGF0dXMiO3M6NjoidW5wYWlkIjtzOjk6ImNsaWVudF9pZCI7Tjt9czoxMToiACoAb3JpZ2luYWwiO2E6MjQ6e3M6MjoiaWQiO2k6NjtzOjQ6Im5hbWUiO3M6MjE6IktLUFIgS2F3YXNhbiBJbmR1c3RyaSI7czoxMToiZGVzY3JpcHRpb24iO3M6OTQ6IkthamlhbiBLZXNlbGFtYXRhbiBkYW4gS2VzZWhhdGFuIEtlcmphIHNlcnRhIFBlcmxpbmR1bmdhbiBSYWRpYXNpIHVudHVrIGthd2FzYW4gaW5kdXN0cmkga2ltaWEiO3M6MTE6ImNsaWVudF9uYW1lIjtzOjI3OiJQVCBJbmR1c3RyaSBLaW1pYSBTZWphaHRlcmEiO3M6MTQ6ImNsaWVudF9hZGRyZXNzIjtzOjMyOiJLYXdhc2FuIEluZHVzdHJpIENpbGVnb24sIEJhbnRlbiI7czo5OiJzdGF0dXNfaWQiO2k6NTtzOjE0OiJpbnN0aXR1dGlvbl9pZCI7aToxO3M6NToibm90ZXMiO3M6Nzk6IlByb3NlcyBkaXR1bmRhIGthcmVuYSBtZW51bmdndSBoYXNpbCBhdWRpdCBzYWZldHkgZGFyaSBrb25zdWx0YW4gaW50ZXJuYXNpb25hbC4iO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTQiO3M6MTQ6ImNsaWVudF9jb250YWN0IjtzOjM5OiIwMjEtNzg5MTIzNCAvIGtpbWlhLnNlamFodGVyYUBlbWFpbC5jb20iO3M6MTA6InN0YXJ0X2RhdGUiO3M6MTA6IjIwMjUtMDgtMTUiO3M6ODoiZGVhZGxpbmUiO3M6MTA6IjIwMjUtMTItMzEiO3M6MTk6InByb2dyZXNzX3BlcmNlbnRhZ2UiO2k6MzA7czo2OiJidWRnZXQiO3M6MTI6IjQwMDAwMDAwMC4wMCI7czoxMToiYWN0dWFsX2Nvc3Qi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Mjp7czo4OiIAKgBpdGVtcyI7YTo2OntpOjA7YTo4OntzOjQ6InR5cGUiO3M6NzoicHJvamVjdCI7czo0OiJpY29uIjtzOjQ6IvCfk4EiO3M6NToidGl0bGUiO3M6MzI6IlBlcml6aW5hbiBJTUIgR2VkdW5nIFBlcmthbnRvcmFuIjtzOjExOiJkZXNjcmlwdGlvbiI7czoxNToiUHJvamVjdCBLb250cmFrIjtzOjQ6InRpbWUiO086MjU6IklsbHVtaW5hdGVcU3VwcG9ydFxDYXJib24iOjM6e3M6NDoiZGF0ZSI7czoyNjoiMjAyNS0xMS0wMyAxMjozMzoxNC4wMDAwMDAiO3M6MTM6InRpbWV6b25lX3R5cGUiO2k6MztzOjg6InRpbWV6b25lIjtzOjM6IlVUQyI7fXM6MTQ6InRpbWVfZm9ybWF0dGVkIjtzOjE4OiIxIG1pbmdndSB5YW5nIGxhbHUiO3M6NDoibGluayI7czoyOToiaHR0cHM6Ly9iaXptYXJrLmlkL3Byb2plY3RzLzEiO3M6NToiY29sb3IiO3M6MjA6InJnYmEoMCwgMTIyLCAyNTUsIDEpIjt9aToxO2E6ODp7czo0OiJ0eXBlIjtzOjc6InByb2plY3QiO3M6NDoiaWNvbiI7czo0OiLwn5OBIjtzOjU6InRpdGxlIjtzOjMyOiJQZXJpemluYW4gVUtMLVVQTCBQYWJyaWsgVGVrc3RpbCI7czoxMToiZGVzY3JpcHRpb24iO3M6Mjc6IlByb2plY3QgUGVuZ3VtcHVsYW4gRG9rdW1lbiI7czo0OiJ0aW1lIjtPOjI1OiJJbGx1bWluYXRlXFN1cHBvcnRcQ2FyYm9uIjozOntzOjQ6ImRhdGUiO3M6MjY6IjIwMjUtMTEtMDMgMTI6MzM6MTQuMDAwMDAwIjtzOjEzOiJ0aW1lem9uZV90eXBlIjtpOjM7czo4OiJ0aW1lem9uZSI7czozOiJVVEMiO31zOjE0OiJ0aW1lX2Zvcm1hdHRlZCI7czoxODoiMSBtaW5nZ3UgeWFuZyBsYWx1IjtzOjQ6ImxpbmsiO3M6Mjk6Imh0dHBzOi8vYml6bWFyay5pZC9wcm9qZWN0cy8yIjtzOjU6ImNvbG9yIjtzOjIwOiJyZ2JhKDAsIDEyMiwgMjU1LCAxKSI7fWk6MjthOjg6e3M6NDoidHlwZSI7czo3OiJwcm9qZWN0IjtzOjQ6Imljb24iO3M6NDoi8J+TgSI7czo1OiJ0aXRsZSI7czozMDoiQW5kYWxhbGluIE1hbGwgU2hvcHBpbmcgQ2VudGVyIjtzOjExOiJkZXNjcmlwdGlvbiI7czoxNzoiUHJvamVjdCBQZW5hd2FyYW4iO3M6NDoidGltZSI7TzoyNToiSWxsdW1pbmF0ZVxTdXBwb3J0XENhcmJvbiI6Mzp7czo0OiJkYXRlIjtzOjI2OiIyMDI1LTExLTAzIDEyOjMzOjE0LjAwMDAwMCI7czoxMzoidGltZXpvbmVfdHlwZSI7aTozO3M6ODoidGltZXpvbmUiO3M6MzoiVVRDIjt9czoxNDoidGltZV9mb3JtYXR0ZWQiO3M6MTg6IjEgbWluZ2d1IHlhbmcgbGFsdSI7czo0OiJsaW5rIjtzOjI5OiJodHRwczovL2Jpem1hcmsuaWQvcHJvamVjdHMvMyI7czo1OiJjb2xvciI7czoyMDoicmdiYSgwLCAxMjIsIDI1NSwgMSkiO31pOjM7YTo4OntzOjQ6InR5cGUiO3M6NzoicHJvamVjdCI7czo0OiJpY29uIjtzOjQ6IvCfk4EiO3M6NToidGl0bGUiO3M6MzE6IlNlcnRpZmlrYXQgSGFsYWwgUHJvZHVrIE1ha2FuYW4iO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjIxOiJQcm9qZWN0IFByb3NlcyBkaSBETEgiO3M6NDoidGltZSI7TzoyNToiSWxsdW1pbmF0ZVxTdXBwb3J0XENhcmJvbiI6Mzp7czo0OiJkYXRlIjtzOjI2OiIyMDI1LTExLTAzIDEyOjMzOjE0LjAwMDAwMCI7czoxMzoidGltZXpvbmVfdHlwZSI7aTozO3M6ODoidGltZXpvbmUiO3M6MzoiVVRDIjt9czoxNDoidGltZV9mb3JtYXR0ZWQiO3M6MTg6IjEgbWluZ2d1IHlhbmcgbGFsdSI7czo0OiJsaW5rIjtzOjI5OiJodHRwczovL2Jpem1hcmsuaWQvcHJvamVjdHMvNCI7czo1OiJjb2xvciI7czoyMDoicmdiYSgwLCAxMjIsIDI1NSwgMSkiO31pOjQ7YTo4OntzOjQ6InR5cGUiO3M6NzoicHJvamVjdCI7czo0OiJpY29uIjtzOjQ6IvCfk4EiO3M6NToidGl0bGUiO3M6MzE6IlBlcml6aW5hbiBPU1MgU3RhcnR1cCBUZWtub2xvZ2kiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjE1OiJQcm9qZWN0IEtvbnRyYWsiO3M6NDoidGltZSI7TzoyNToiSWxsdW1pbmF0ZVxTdXBwb3J0XENhcmJvbiI6Mzp7czo0OiJkYXRlIjtzOjI2OiIyMDI1LTExLTAzIDEyOjMzOjE0LjAwMDAwMCI7czoxMzoidGltZXpvbmVfdHlwZSI7aTozO3M6ODoidGltZXpvbmUiO3M6MzoiVVRDIjt9czoxNDoidGltZV9mb3JtYXR0ZWQiO3M6MTg6IjEgbWluZ2d1IHlhbmcgbGFsdSI7czo0OiJsaW5rIjtzOjI5OiJodHRwczovL2Jpem1hcmsuaWQvcHJvamVjdHMvNSI7czo1OiJjb2xvciI7czoyMDoicmdiYSgwLCAxMjIsIDI1NSwgMSkiO31pOjU7YTo4OntzOjQ6InR5cGUiO3M6NDoidGFzayI7czo0OiJpY29uIjtzOjM6IuKchSI7czo1OiJ0aXRsZSI7czoyNjoiRmluYWxpc2FzaSBEb2t1bWVuIFVLTC1VUEwiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjE0OiJUYXNrIGNvbXBsZXRlZCI7czo0OiJ0aW1lIjtPOjI1OiJJbGx1bWluYXRlXFN1cHBvcnRcQ2FyYm9uIjozOntzOjQ6ImRhdGUiO3M6MjY6IjIwMjUtMTEtMDMgMTI6MzM6MTQuMDAwMDAwIjtzOjEzOiJ0aW1lem9uZV90eXBlIjtpOjM7czo4OiJ0aW1lem9uZSI7czozOiJVVEMiO31zOjE0OiJ0aW1lX2Zvcm1hdHRlZCI7czoxODoiMSBtaW5nZ3UgeWFuZyBsYWx1IjtzOjQ6ImxpbmsiO3M6MjY6Imh0dHBzOi8vYml6bWFyay5pZC90YXNrcy84IjtzOjU6ImNvbG9yIjtzOjIwOiJyZ2JhKDUyLCAxOTksIDg5LCAxKSI7fX1zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fXM6NToiY291bnQiO2k6Njt9fQ==	1763032464
laravel-cache-expense_categories.options	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	2078392190
laravel-cache-payment_methods.options	TzozOToiSWxsdW1pbmF0ZVxEYXRhYmFzZVxFbG9xdWVudFxDb2xsZWN0aW9uIjoyOntzOjg6IgAqAGl0ZW1zIjthOjU6e2k6MDtPOjI0OiJBcHBcTW9kZWxzXFBheW1lbnRNZXRob2QiOjMzOntzOjEzOiIAKgBjb25uZWN0aW9uIjtzOjU6InBnc3FsIjtzOjg6IgAqAHRhYmxlIjtzOjE1OiJwYXltZW50X21ldGhvZHMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YToxMDp7czoyOiJpZCI7aToxO3M6NDoiY29kZSI7czoxMzoiYmFua190cmFuc2ZlciI7czo0OiJuYW1lIjtzOjEzOiJUcmFuc2ZlciBCYW5rIjtzOjExOiJkZXNjcmlwdGlvbiI7czozOToiUGVtYmF5YXJhbiBtZWxhbHVpIHRyYW5zZmVyIGFudGFyIGJhbmsuIjtzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO2I6MTtzOjEwOiJpc19kZWZhdWx0IjtiOjE7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEwOiJzb3J0X29yZGVyIjtpOjEwO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO31zOjExOiIAKgBvcmlnaW5hbCI7YToxMDp7czoyOiJpZCI7aToxO3M6NDoiY29kZSI7czoxMzoiYmFua190cmFuc2ZlciI7czo0OiJuYW1lIjtzOjEzOiJUcmFuc2ZlciBCYW5rIjtzOjExOiJkZXNjcmlwdGlvbiI7czozOToiUGVtYmF5YXJhbiBtZWxhbHVpIHRyYW5zZmVyIGFudGFyIGJhbmsuIjtzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO2I6MTtzOjEwOiJpc19kZWZhdWx0IjtiOjE7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEwOiJzb3J0X29yZGVyIjtpOjEwO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjExOiIAKgBwcmV2aW91cyI7YTowOnt9czo4OiIAKgBjYXN0cyI7YTozOntzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO3M6NzoiYm9vbGVhbiI7czoxMDoiaXNfZGVmYXVsdCI7czo3OiJib29sZWFuIjtzOjk6ImlzX2FjdGl2ZSI7czo3OiJib29sZWFuIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjA6e31zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjI3OiIAKgByZWxhdGlvbkF1dG9sb2FkQ2FsbGJhY2siO047czoyNjoiACoAcmVsYXRpb25BdXRvbG9hZENvbnRleHQiO047czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTM6InVzZXNVbmlxdWVJZHMiO2I6MDtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6Nzp7aTowO3M6NDoiY29kZSI7aToxO3M6NDoibmFtZSI7aToyO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjM7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtpOjQ7czoxMDoiaXNfZGVmYXVsdCI7aTo1O3M6OToiaXNfYWN0aXZlIjtpOjY7czoxMDoic29ydF9vcmRlciI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX1pOjE7TzoyNDoiQXBwXE1vZGVsc1xQYXltZW50TWV0aG9kIjozMzp7czoxMzoiACoAY29ubmVjdGlvbiI7czo1OiJwZ3NxbCI7czo4OiIAKgB0YWJsZSI7czoxNToicGF5bWVudF9tZXRob2RzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MTA6e3M6MjoiaWQiO2k6MjtzOjQ6ImNvZGUiO3M6NDoiY2FzaCI7czo0OiJuYW1lIjtzOjk6IkthcyBUdW5haSI7czoxMToiZGVzY3JpcHRpb24iO3M6NDM6IlBlbWJheWFyYW4gbGFuZ3N1bmcgbWVuZ2d1bmFrYW4gdWFuZyB0dW5haS4iO3M6MjE6InJlcXVpcmVzX2Nhc2hfYWNjb3VudCI7YjoxO3M6MTA6ImlzX2RlZmF1bHQiO2I6MDtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6MTA6InNvcnRfb3JkZXIiO2k6MjA7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjEwOntzOjI6ImlkIjtpOjI7czo0OiJjb2RlIjtzOjQ6ImNhc2giO3M6NDoibmFtZSI7czo5OiJLYXMgVHVuYWkiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjQzOiJQZW1iYXlhcmFuIGxhbmdzdW5nIG1lbmdndW5ha2FuIHVhbmcgdHVuYWkuIjtzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO2I6MTtzOjEwOiJpc19kZWZhdWx0IjtiOjA7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEwOiJzb3J0X29yZGVyIjtpOjIwO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjExOiIAKgBwcmV2aW91cyI7YTowOnt9czo4OiIAKgBjYXN0cyI7YTozOntzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO3M6NzoiYm9vbGVhbiI7czoxMDoiaXNfZGVmYXVsdCI7czo3OiJib29sZWFuIjtzOjk6ImlzX2FjdGl2ZSI7czo3OiJib29sZWFuIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjA6e31zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjI3OiIAKgByZWxhdGlvbkF1dG9sb2FkQ2FsbGJhY2siO047czoyNjoiACoAcmVsYXRpb25BdXRvbG9hZENvbnRleHQiO047czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTM6InVzZXNVbmlxdWVJZHMiO2I6MDtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6Nzp7aTowO3M6NDoiY29kZSI7aToxO3M6NDoibmFtZSI7aToyO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjM7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtpOjQ7czoxMDoiaXNfZGVmYXVsdCI7aTo1O3M6OToiaXNfYWN0aXZlIjtpOjY7czoxMDoic29ydF9vcmRlciI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX1pOjI7TzoyNDoiQXBwXE1vZGVsc1xQYXltZW50TWV0aG9kIjozMzp7czoxMzoiACoAY29ubmVjdGlvbiI7czo1OiJwZ3NxbCI7czo4OiIAKgB0YWJsZSI7czoxNToicGF5bWVudF9tZXRob2RzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MTA6e3M6MjoiaWQiO2k6MztzOjQ6ImNvZGUiO3M6NToiY2hlY2siO3M6NDoibmFtZSI7czozOiJDZWsiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjI3OiJQZW1iYXlhcmFuIG1lbmdndW5ha2FuIGNlay4iO3M6MjE6InJlcXVpcmVzX2Nhc2hfYWNjb3VudCI7YjoxO3M6MTA6ImlzX2RlZmF1bHQiO2I6MDtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6MTA6InNvcnRfb3JkZXIiO2k6MzA7czoxMDoiY3JlYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7czoxMDoidXBkYXRlZF9hdCI7czoxOToiMjAyNS0xMS0wMyAxMjozMzoxMyI7fXM6MTE6IgAqAG9yaWdpbmFsIjthOjEwOntzOjI6ImlkIjtpOjM7czo0OiJjb2RlIjtzOjU6ImNoZWNrIjtzOjQ6Im5hbWUiO3M6MzoiQ2VrIjtzOjExOiJkZXNjcmlwdGlvbiI7czoyNzoiUGVtYmF5YXJhbiBtZW5nZ3VuYWthbiBjZWsuIjtzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO2I6MTtzOjEwOiJpc19kZWZhdWx0IjtiOjA7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEwOiJzb3J0X29yZGVyIjtpOjMwO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjExOiIAKgBwcmV2aW91cyI7YTowOnt9czo4OiIAKgBjYXN0cyI7YTozOntzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO3M6NzoiYm9vbGVhbiI7czoxMDoiaXNfZGVmYXVsdCI7czo3OiJib29sZWFuIjtzOjk6ImlzX2FjdGl2ZSI7czo3OiJib29sZWFuIjt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjA6e31zOjEwOiIAKgB0b3VjaGVzIjthOjA6e31zOjI3OiIAKgByZWxhdGlvbkF1dG9sb2FkQ2FsbGJhY2siO047czoyNjoiACoAcmVsYXRpb25BdXRvbG9hZENvbnRleHQiO047czoxMDoidGltZXN0YW1wcyI7YjoxO3M6MTM6InVzZXNVbmlxdWVJZHMiO2I6MDtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6Nzp7aTowO3M6NDoiY29kZSI7aToxO3M6NDoibmFtZSI7aToyO3M6MTE6ImRlc2NyaXB0aW9uIjtpOjM7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtpOjQ7czoxMDoiaXNfZGVmYXVsdCI7aTo1O3M6OToiaXNfYWN0aXZlIjtpOjY7czoxMDoic29ydF9vcmRlciI7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX1pOjM7TzoyNDoiQXBwXE1vZGVsc1xQYXltZW50TWV0aG9kIjozMzp7czoxMzoiACoAY29ubmVjdGlvbiI7czo1OiJwZ3NxbCI7czo4OiIAKgB0YWJsZSI7czoxNToicGF5bWVudF9tZXRob2RzIjtzOjEzOiIAKgBwcmltYXJ5S2V5IjtzOjI6ImlkIjtzOjEwOiIAKgBrZXlUeXBlIjtzOjM6ImludCI7czoxMjoiaW5jcmVtZW50aW5nIjtiOjE7czo3OiIAKgB3aXRoIjthOjA6e31zOjEyOiIAKgB3aXRoQ291bnQiO2E6MDp7fXM6MTk6InByZXZlbnRzTGF6eUxvYWRpbmciO2I6MDtzOjEwOiIAKgBwZXJQYWdlIjtpOjE1O3M6NjoiZXhpc3RzIjtiOjE7czoxODoid2FzUmVjZW50bHlDcmVhdGVkIjtiOjA7czoyODoiACoAZXNjYXBlV2hlbkNhc3RpbmdUb1N0cmluZyI7YjowO3M6MTM6IgAqAGF0dHJpYnV0ZXMiO2E6MTA6e3M6MjoiaWQiO2k6NDtzOjQ6ImNvZGUiO3M6NDoiZ2lybyI7czo0OiJuYW1lIjtzOjQ6Ikdpcm8iO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjM1OiJQZW1iYXlhcmFuIG1lbmdndW5ha2FuIGdpcm8vYmlseWV0LiI7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtiOjE7czoxMDoiaXNfZGVmYXVsdCI7YjowO3M6OToiaXNfYWN0aXZlIjtiOjE7czoxMDoic29ydF9vcmRlciI7aTo0MDtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMToiACoAb3JpZ2luYWwiO2E6MTA6e3M6MjoiaWQiO2k6NDtzOjQ6ImNvZGUiO3M6NDoiZ2lybyI7czo0OiJuYW1lIjtzOjQ6Ikdpcm8iO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjM1OiJQZW1iYXlhcmFuIG1lbmdndW5ha2FuIGdpcm8vYmlseWV0LiI7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtiOjE7czoxMDoiaXNfZGVmYXVsdCI7YjowO3M6OToiaXNfYWN0aXZlIjtiOjE7czoxMDoic29ydF9vcmRlciI7aTo0MDtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxMToiACoAcHJldmlvdXMiO2E6MDp7fXM6ODoiACoAY2FzdHMiO2E6Mzp7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtzOjc6ImJvb2xlYW4iO3M6MTA6ImlzX2RlZmF1bHQiO3M6NzoiYm9vbGVhbiI7czo5OiJpc19hY3RpdmUiO3M6NzoiYm9vbGVhbiI7fXM6MTc6IgAqAGNsYXNzQ2FzdENhY2hlIjthOjA6e31zOjIxOiIAKgBhdHRyaWJ1dGVDYXN0Q2FjaGUiO2E6MDp7fXM6MTM6IgAqAGRhdGVGb3JtYXQiO047czoxMDoiACoAYXBwZW5kcyI7YTowOnt9czoxOToiACoAZGlzcGF0Y2hlc0V2ZW50cyI7YTowOnt9czoxNDoiACoAb2JzZXJ2YWJsZXMiO2E6MDp7fXM6MTI6IgAqAHJlbGF0aW9ucyI7YTowOnt9czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoyNzoiACoAcmVsYXRpb25BdXRvbG9hZENhbGxiYWNrIjtOO3M6MjY6IgAqAHJlbGF0aW9uQXV0b2xvYWRDb250ZXh0IjtOO3M6MTA6InRpbWVzdGFtcHMiO2I6MTtzOjEzOiJ1c2VzVW5pcXVlSWRzIjtiOjA7czo5OiIAKgBoaWRkZW4iO2E6MDp7fXM6MTA6IgAqAHZpc2libGUiO2E6MDp7fXM6MTE6IgAqAGZpbGxhYmxlIjthOjc6e2k6MDtzOjQ6ImNvZGUiO2k6MTtzOjQ6Im5hbWUiO2k6MjtzOjExOiJkZXNjcmlwdGlvbiI7aTozO3M6MjE6InJlcXVpcmVzX2Nhc2hfYWNjb3VudCI7aTo0O3M6MTA6ImlzX2RlZmF1bHQiO2k6NTtzOjk6ImlzX2FjdGl2ZSI7aTo2O3M6MTA6InNvcnRfb3JkZXIiO31zOjEwOiIAKgBndWFyZGVkIjthOjE6e2k6MDtzOjE6IioiO319aTo0O086MjQ6IkFwcFxNb2RlbHNcUGF5bWVudE1ldGhvZCI6MzM6e3M6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToicGdzcWwiO3M6ODoiACoAdGFibGUiO3M6MTU6InBheW1lbnRfbWV0aG9kcyI7czoxMzoiACoAcHJpbWFyeUtleSI7czoyOiJpZCI7czoxMDoiACoAa2V5VHlwZSI7czozOiJpbnQiO3M6MTI6ImluY3JlbWVudGluZyI7YjoxO3M6NzoiACoAd2l0aCI7YTowOnt9czoxMjoiACoAd2l0aENvdW50IjthOjA6e31zOjE5OiJwcmV2ZW50c0xhenlMb2FkaW5nIjtiOjA7czoxMDoiACoAcGVyUGFnZSI7aToxNTtzOjY6ImV4aXN0cyI7YjoxO3M6MTg6Indhc1JlY2VudGx5Q3JlYXRlZCI7YjowO3M6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDtzOjEzOiIAKgBhdHRyaWJ1dGVzIjthOjEwOntzOjI6ImlkIjtpOjU7czo0OiJjb2RlIjtzOjU6Im90aGVyIjtzOjQ6Im5hbWUiO3M6MTQ6Ik1ldG9kZSBMYWlubnlhIjtzOjExOiJkZXNjcmlwdGlvbiI7czo0NDoiTWV0b2RlIHBlbWJheWFyYW4ga2h1c3VzIHNlc3VhaSBrZXNlcGFrYXRhbi4iO3M6MjE6InJlcXVpcmVzX2Nhc2hfYWNjb3VudCI7YjowO3M6MTA6ImlzX2RlZmF1bHQiO2I6MDtzOjk6ImlzX2FjdGl2ZSI7YjoxO3M6MTA6InNvcnRfb3JkZXIiO2k6MTAwO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjUtMTEtMDMgMTI6MzM6MTMiO31zOjExOiIAKgBvcmlnaW5hbCI7YToxMDp7czoyOiJpZCI7aTo1O3M6NDoiY29kZSI7czo1OiJvdGhlciI7czo0OiJuYW1lIjtzOjE0OiJNZXRvZGUgTGFpbm55YSI7czoxMToiZGVzY3JpcHRpb24iO3M6NDQ6Ik1ldG9kZSBwZW1iYXlhcmFuIGtodXN1cyBzZXN1YWkga2VzZXBha2F0YW4uIjtzOjIxOiJyZXF1aXJlc19jYXNoX2FjY291bnQiO2I6MDtzOjEwOiJpc19kZWZhdWx0IjtiOjA7czo5OiJpc19hY3RpdmUiO2I6MTtzOjEwOiJzb3J0X29yZGVyIjtpOjEwMDtzOjEwOiJjcmVhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjE5OiIyMDI1LTExLTAzIDEyOjMzOjEzIjt9czoxMDoiACoAY2hhbmdlcyI7YTowOnt9czoxMToiACoAcHJldmlvdXMiO2E6MDp7fXM6ODoiACoAY2FzdHMiO2E6Mzp7czoyMToicmVxdWlyZXNfY2FzaF9hY2NvdW50IjtzOjc6ImJvb2xlYW4iO3M6MTA6ImlzX2RlZmF1bHQiO3M6NzoiYm9vbGVhbiI7czo5OiJpc19hY3RpdmUiO3M6NzoiYm9vbGVhbiI7fXM6MTc6IgAqAGNsYXNzQ2FzdENhY2hlIjthOjA6e31zOjIxOiIAKgBhdHRyaWJ1dGVDYXN0Q2FjaGUiO2E6MDp7fXM6MTM6IgAqAGRhdGVGb3JtYXQiO047czoxMDoiACoAYXBwZW5kcyI7YTowOnt9czoxOToiACoAZGlzcGF0Y2hlc0V2ZW50cyI7YTowOnt9czoxNDoiACoAb2JzZXJ2YWJsZXMiO2E6MDp7fXM6MTI6IgAqAHJlbGF0aW9ucyI7YTowOnt9czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoyNzoiACoAcmVsYXRpb25BdXRvbG9hZENhbGxiYWNrIjtOO3M6MjY6IgAqAHJlbGF0aW9uQXV0b2xvYWRDb250ZXh0IjtOO3M6MTA6InRpbWVzdGFtcHMiO2I6MTtzOjEzOiJ1c2VzVW5pcXVlSWRzIjtiOjA7czo5OiIAKgBoaWRkZW4iO2E6MDp7fXM6MTA6IgAqAHZpc2libGUiO2E6MDp7fXM6MTE6IgAqAGZpbGxhYmxlIjthOjc6e2k6MDtzOjQ6ImNvZGUiO2k6MTtzOjQ6Im5hbWUiO2k6MjtzOjExOiJkZXNjcmlwdGlvbiI7aTozO3M6MjE6InJlcXVpcmVzX2Nhc2hfYWNjb3VudCI7aTo0O3M6MTA6ImlzX2RlZmF1bHQiO2k6NTtzOjk6ImlzX2FjdGl2ZSI7aTo2O3M6MTA6InNvcnRfb3JkZXIiO31zOjEwOiIAKgBndWFyZGVkIjthOjE6e2k6MDtzOjE6IioiO319fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9	2078392190
laravel-cache-nav_counts	a:7:{s:8:"projects";i:6;s:5:"tasks";i:8;s:9:"documents";i:7;s:12:"institutions";i:6;s:13:"cash_accounts";i:0;s:12:"permit_types";i:0;s:16:"permit_templates";i:0;}	1763033888
laravel-cache-landing.latest_articles	TzozOToiSWxsdW1pbmF0ZVxEYXRhYmFzZVxFbG9xdWVudFxDb2xsZWN0aW9uIjoyOntzOjg6IgAqAGl0ZW1zIjthOjA6e31zOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7fQ==	1763034409
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: cash_accounts; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.cash_accounts (id, account_name, account_type, account_number, bank_name, account_holder, current_balance, initial_balance, is_active, notes, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.clients (id, name, company_name, industry, contact_person, email, phone, mobile, address, city, province, postal_code, npwp, tax_name, tax_address, client_type, status, notes, created_at, updated_at, deleted_at, password, email_verified_at, remember_token) FROM stdin;
\.


--
-- Data for Name: compliance_checks; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.compliance_checks (id, draft_id, document_type, overall_score, structure_score, compliance_score, formatting_score, completeness_score, issues, status, error_message, total_issues, critical_issues, warning_issues, info_issues, checked_at, created_at, updated_at) FROM stdin;
1	14	UKL-UPL	72.50	100.00	50.00	100.00	60.00	[{"message": "Formulir UKL-UPL tidak lengkap (hanya 4 dari 11+ kolom ditemukan)", "category": "compliance", "location": "BAB III.5 - Formulir UKL-UPL", "severity": "critical", "suggestion": "Gunakan format tabel UKL-UPL standar dengan 12 kolom: Dampak, Sumber, Indikator, Pengelolaan (Bentuk/Lokasi/Periode/Institusi), Pemantauan (Bentuk/Lokasi/Periode/Institusi)"}, {"message": "NIK/No. KTP tidak tercantum", "category": "completeness", "location": "BAB II.1", "severity": "info", "suggestion": "Sebaiknya tambahkan NIK/No. KTP untuk kelengkapan data"}, {"message": "NPWP tidak tercantum", "category": "completeness", "location": "BAB II.1", "severity": "info", "suggestion": "Jika pemrakarsa memiliki NPWP, sebaiknya dicantumkan"}, {"message": "Dokumen terlalu pendek (135 kata, minimum 1000 kata)", "category": "completeness", "location": "Document-wide", "severity": "critical", "suggestion": "UKL-UPL standar minimal 10-15 halaman. Lengkapi semua bagian dengan detail yang memadai"}]	completed	\N	4	2	0	2	2025-11-03 17:31:35	2025-11-03 17:13:04	2025-11-03 17:31:35
\.


--
-- Data for Name: document_drafts; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.document_drafts (id, project_id, template_id, ai_log_id, title, content, sections, status, created_by, approved_at, approved_by, created_at, updated_at, deleted_at) FROM stdin;
3	1	1	12	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	rejected	1	\N	\N	2025-11-03 14:06:40	2025-11-03 16:34:30	2025-11-03 16:34:30
12	1	1	36	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran	SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK) Kepada Yth.\n\nKepala Kantor Pertanahan Jakarta Selatan di Tempat Dengan hormat, Yang bertanda tangan di bawah ini: Nama: PT Mega Prima Development Alamat: Jl. Sudirman No. 123, Jakarta Selatan Mengajukan permohonan Pertek untuk: Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Luas: N/A m² Sertifikat: N/A Demikian permohonan ini kami sampaikan.\n\nTerima kasih.\n\nJakarta Selatan, 15 September 2025 (PT Mega Prima Development)	[{"chunk_index":1,"content":"SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK) Kepada Yth.\\n\\nKepala Kantor Pertanahan Jakarta Selatan di Tempat Dengan hormat, Yang bertanda tangan di bawah ini: Nama: PT Mega Prima Development Alamat: Jl. Sudirman No. 123, Jakarta Selatan Mengajukan permohonan Pertek untuk: Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Luas: N\\/A m\\u00b2 Sertifikat: N\\/A Demikian permohonan ini kami sampaikan.\\n\\nTerima kasih.\\n\\nJakarta Selatan, 15 September 2025 (PT Mega Prima Development)","input_tokens":903,"output_tokens":850}]	draft	1	\N	\N	2025-11-03 14:20:41	2025-11-03 16:34:35	2025-11-03 16:34:35
9	1	1	33	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran	SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK) Kepada Yth.\n\nKepala Kantor Pertanahan Jakarta Selatan di Tempat Dengan hormat, Yang bertanda tangan di bawah ini: Nama: PT Mega Prima Development Alamat: Jl. Sudirman No. 123, Jakarta Selatan Mengajukan permohonan Pertek untuk: Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Luas: N/A m² Sertifikat: N/A Demikian permohonan ini kami sampaikan.\n\nTerima kasih.\n\nJl. Sudirman No. 123, Jakarta Selatan, 15 September 2025 (PT Mega Prima Development)	[{"chunk_index":1,"content":"SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK) Kepada Yth.\\n\\nKepala Kantor Pertanahan Jakarta Selatan di Tempat Dengan hormat, Yang bertanda tangan di bawah ini: Nama: PT Mega Prima Development Alamat: Jl. Sudirman No. 123, Jakarta Selatan Mengajukan permohonan Pertek untuk: Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Luas: N\\/A m\\u00b2 Sertifikat: N\\/A Demikian permohonan ini kami sampaikan.\\n\\nTerima kasih.\\n\\nJl. Sudirman No. 123, Jakarta Selatan, 15 September 2025 (PT Mega Prima Development)","input_tokens":903,"output_tokens":1109}]	draft	1	\N	\N	2025-11-03 14:16:44	2025-11-03 16:34:43	2025-11-03 16:34:43
8	1	1	17	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 14:09:43	2025-11-03 16:34:45	2025-11-03 16:34:45
7	1	1	16	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 14:09:31	2025-11-03 16:34:48	2025-11-03 16:34:48
6	1	1	15	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 14:08:54	2025-11-03 16:34:50	2025-11-03 16:34:50
5	1	1	14	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 14:08:00	2025-11-03 16:34:52	2025-11-03 16:34:52
4	1	1	13	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 14:06:52	2025-11-03 16:34:55	2025-11-03 16:34:55
2	1	1	11	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 14:04:26	2025-11-03 16:34:59	2025-11-03 16:34:59
1	1	1	10	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran		[{"chunk_index":1,"content":"","input_tokens":0,"output_tokens":0}]	draft	1	\N	\N	2025-11-03 13:59:42	2025-11-03 16:35:01	2025-11-03 16:35:01
11	1	1	35	Template Permohonan Pertek BPN - Perizinan IMB Gedung Perkantoran	SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK) Kepada Yth.\n\nKepala Kantor Pertanahan Jakarta Selatan di Tempat Dengan hormat, Yang bertanda tangan di bawah ini: Nama: PT Mega Prima Development Alamat: Jl. Sudirman No. 123, Jakarta Selatan Mengajukan permohonan Pertek untuk: Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Luas: N/A m² Sertifikat: N/A Demikian permohonan ini kami sampaikan.\n\nTerima kasih.\n\nJakarta Selatan, 15 September 2025 (PT Mega Prima Development)	[{"chunk_index":1,"content":"SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK) Kepada Yth.\\n\\nKepala Kantor Pertanahan Jakarta Selatan di Tempat Dengan hormat, Yang bertanda tangan di bawah ini: Nama: PT Mega Prima Development Alamat: Jl. Sudirman No. 123, Jakarta Selatan Mengajukan permohonan Pertek untuk: Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Luas: N\\/A m\\u00b2 Sertifikat: N\\/A Demikian permohonan ini kami sampaikan.\\n\\nTerima kasih.\\n\\nJakarta Selatan, 15 September 2025 (PT Mega Prima Development)","input_tokens":903,"output_tokens":1382}]	draft	1	\N	\N	2025-11-03 14:20:36	2025-11-03 16:34:37	2025-11-03 16:34:37
10	1	2	34	Template Dokumen UKL-UPL - Perizinan IMB Gedung Perkantoran	DOKUMEN UKL-UPL Pemrakarsa: PT Mega Prima Development Kegiatan: Perizinan IMB Gedung Perkantoran Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Jenis: Katering Luas: 10000 m² BAB I - PENDAHULUAN Dokumen ini menerangkan upaya pengelolaan dan pemantauan lingkungan.\r\n\r\nBAB II - DESKRIPSI KEGIATAN Pengurusan Izin Mendirikan Bangunan untuk gedung perkantoran 8 lantai di kawasan bisnis Jakarta Selatan BAB III - DAMPAK LINGKUNGAN [Dampak yang mungkin timbul] BAB IV - PENGELOLAAN [Upaya pengelolaan] Jl. Sudirman No. 123, Jakarta Selatan, 15 September 2025 (PT Mega Prima Development)	[{"chunk_index":1,"content":"DOKUMEN UKL-UPL Pemrakarsa: PT Mega Prima Development Kegiatan: Perizinan IMB Gedung Perkantoran Lokasi: Jl. Sudirman No. 123, Jakarta Selatan Jenis: Katering Luas: 10000 m\\u00b2 BAB I - PENDAHULUAN Dokumen ini menerangkan upaya pengelolaan dan pemantauan lingkungan.\\n\\nBAB II - DESKRIPSI KEGIATAN Pengurusan Izin Mendirikan Bangunan untuk gedung perkantoran 8 lantai di kawasan bisnis Jakarta Selatan BAB III - DAMPAK LINGKUNGAN [Dampak yang mungkin timbul] BAB IV - PENGELOLAAN [Upaya pengelolaan] Jl. Sudirman No. 123, Jakarta Selatan, 15 September 2025 (PT Mega Prima Development)","input_tokens":929,"output_tokens":851}]	draft	1	\N	\N	2025-11-03 14:18:39	2025-11-03 16:34:40	2025-11-03 16:34:40
14	1	1	40	UKL-UPL Test Draft	BAB I: PENDAHULUAN\r\n\r\n1.1. Latar Belakang\r\nRencana pembangunan rumah kost dan ruko ini bertujuan untuk memenuhi kebutuhan hunian.\r\n\r\n1.2. Tujuan dan Manfaat\r\nTujuan penyusunan dokumen UKL-UPL ini adalah untuk memastikan kegiatan dilaksanakan sesuai regulasi.\r\n\r\n1.3. Peraturan Terkait\r\n- UU No. 32 Tahun 2009 tentang Perlindungan dan Pengelolaan Lingkungan Hidup\r\n- PP No. 22 Tahun 2021\r\n\r\nBAB II: RENCANA USAHA DAN/ATAU KEGIATAN\r\n\r\n2.1. Identitas Pemrakarsa\r\nNama Pemrakarsa: Ibu Maulida\r\nAlamat: Jl. Kalimantan, Kabupaten Bangka\r\nTelepon: 0812-3456-7890\r\nEmail: maulida@example.com\r\n\r\n2.2. Rencana Usaha\r\n2.2.1. Nama: Pembangunan Rumah Kost\r\n2.2.2. Lokasi: Jl. Kalimantan, Kabupaten Bangka\r\n2.2.3. Luas Lahan: 500 m²\r\n\r\nBAB III: DAMPAK PENTING DAN UPAYA PENGELOLAAN\r\n\r\n3.1. Identifikasi Dampak\r\nDampak: kualitas udara, kebisingan, air limbah, sampah\r\n\r\n3.5. FORMULIR UKL-UPL\r\nTabel pengelolaan lingkungan mencakup:\r\n- Dampak Lingkungan: Penurunan kualitas udara\r\n- Sumber Dampak: Konstruksi\r\n- Indikator Dampak: TSP\r\n- Bentuk Pengelolaan: Penyiraman 2x/hari\r\n\r\nBAB IV: KESIMPULAN\r\nKesimpulan dari analisis...	\N	draft	1	\N	\N	2025-11-03 17:12:47	2025-11-03 17:27:33	\N
\.


--
-- Data for Name: document_templates; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.document_templates (id, name, permit_type, description, file_name, file_path, file_size, mime_type, page_count, required_fields, is_active, created_by, created_at, updated_at, deleted_at) FROM stdin;
2	Template Dokumen UKL-UPL	ukl_upl	Template dokumen Upaya Pengelolaan Lingkungan dan Upaya Pemantauan Lingkungan. Untuk kegiatan yang berdampak lingkungan kecil-menengah.	sample_ukl_upl.txt	templates/sample_ukl_upl.txt	403	text/plain	15	["project_name","client_name","location","business_type","land_area","building_area"]	t	1	2025-11-03 13:32:46	2025-11-03 13:32:46	\N
3	Template Permohonan IMB	imb	Template surat permohonan Izin Mendirikan Bangunan (IMB) atau PBG. Untuk pembangunan gedung/bangunan.	sample_imb.txt	templates/sample_imb.txt	365	text/plain	8	["project_name","client_name","location","building_area","land_certificate"]	t	1	2025-11-03 13:32:46	2025-11-03 13:32:46	\N
1	Template Permohonan Pertek BPN	pertek_bpn	Template surat permohonan pertimbangan teknis pertanahan ke BPN. Cocok untuk perizinan yang memerlukan analisa pertanahan.	sample_pertek_bpn.txt	templates/sample_pertek_bpn.txt	413	text/plain	5	\N	t	1	2025-11-03 13:32:46	2025-11-03 13:32:46	\N
\.


--
-- Data for Name: documents; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.documents (id, project_id, task_id, title, description, category, document_type, file_name, file_path, file_size, mime_type, version, parent_document_id, is_latest_version, status, document_date, submission_date, approval_date, uploaded_by, is_confidential, access_permissions, notes, download_count, last_accessed_at, created_at, updated_at) FROM stdin;
1	1	\N	Dokumen IMB - Gambar Teknik	Gambar teknik dan site plan untuk pengajuan IMB kawasan komersial. Termasuk denah, tampak, potongan, dan detail struktur.	teknis	\N	Gambar_Teknik_IMB_2024.pdf	documents/sample_gambar_teknik.pdf	2480000	application/pdf	1.0	\N	t	draft	\N	\N	\N	1	f	\N	\N	0	\N	2025-10-19 12:33:14	2025-10-19 12:33:14
2	2	\N	Dokumen UKL-UPL - Analisis Dampak	Dokumen analisis dampak lingkungan untuk izin UKL-UPL pembangunan pabrik. Mencakup analisis kualitas air, udara, dan pengelolaan limbah.	lingkungan	\N	Analisis_Dampak_Lingkungan.pdf	documents/sample_ukl_upl.pdf	1850000	application/pdf	2.1	\N	t	review	\N	\N	\N	1	f	\N	\N	0	\N	2025-10-14 12:33:14	2025-10-29 12:33:14
3	3	\N	Survei Lalu Lintas - Andalalin	Data hasil survei lalu lintas untuk analisis dampak lalin (Andalalin). Berisi counting kendaraan, waktu tempuh, dan analisis kapasitas jalan.	transportasi	\N	Data_Survei_Lalu_Lintas.xlsx	documents/sample_survei_lalin.xlsx	680000	application/vnd.openxmlformats-officedocument.spreadsheetml.sheet	1.0	\N	t	approved	\N	\N	\N	1	f	\N	\N	0	\N	2025-10-04 12:33:14	2025-10-24 12:33:14
5	1	\N	Surat Permohonan Resmi	Surat permohonan resmi untuk pengajuan IMB yang telah ditandatangani direktur dan bermaterai. Format sesuai template instansi terkait.	administrasi	\N	Surat_Permohonan_IMB.docx	documents/sample_surat_permohonan.docx	95000	application/vnd.openxmlformats-officedocument.wordprocessingml.document	1.0	\N	t	approved	\N	\N	\N	1	f	\N	\N	0	\N	2025-10-09 12:33:14	2025-10-16 12:33:14
6	2	\N	Laporan Monitoring Lingkungan	Laporan monitoring dampak lingkungan kuartal 1 tahun 2024. Mencakup pemantauan kualitas air, udara, kebisingan, dan pengelolaan limbah.	lingkungan	\N	Laporan_Monitoring_Q1_2024.pdf	documents/sample_monitoring_lingkungan.pdf	3200000	application/pdf	1.2	\N	t	review	\N	\N	\N	1	f	\N	\N	0	\N	2025-10-22 12:33:14	2025-11-01 12:33:14
4	1	\N	Peta Lokasi dan Aksesibilitas	Peta lokasi proyek dengan detail aksesibilitas, infrastruktur pendukung, dan radius dampak pembangunan.	teknis	\N	Peta_Lokasi_Proyek.jpg	documents/sample_peta_lokasi.jpg	1200000	image/jpeg	1.0	\N	t	draft	\N	\N	\N	1	f	\N	\N	1	2025-11-03 12:57:53	2025-10-26 12:33:14	2025-11-03 12:57:53
7	1	1	rekapTanpaPerbaikan	\N	kajian	DOC	rekapTanpaPerbaikan.doc	documents/1762177355_rekaptanpaperbaikan.doc	16967	application/vnd.openxmlformats-officedocument.wordprocessingml.document	1.0	\N	t	draft	\N	\N	\N	1	f	\N	\N	1	2025-11-03 13:42:35	2025-11-03 13:42:35	2025-11-03 13:42:35
\.


--
-- Data for Name: email_campaigns; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.email_campaigns (id, name, subject, template_id, content, plain_content, status, recipient_type, recipient_tags, scheduled_at, sent_at, total_recipients, sent_count, opened_count, clicked_count, bounced_count, unsubscribed_count, created_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: email_inbox; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.email_inbox (id, message_id, from_email, from_name, to_email, subject, body_html, body_text, attachments, is_read, is_starred, category, labels, replied_to, assigned_to, received_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: email_logs; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.email_logs (id, campaign_id, subscriber_id, recipient_email, subject, status, sent_at, opened_at, clicked_at, bounced_at, tracking_id, error_message, ip_address, user_agent, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: email_subscribers; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.email_subscribers (id, email, name, phone, status, source, tags, custom_fields, subscribed_at, unsubscribed_at, unsubscribe_reason, created_at, updated_at) FROM stdin;
1	subscriber1@example.com	John Doe	\N	active	landing_page	["customer","active"]	\N	2025-11-13 08:45:45	\N	\N	2025-11-13 08:45:45	2025-11-13 08:45:45
2	subscriber2@example.com	Jane Smith	\N	active	landing_page	["prospect"]	\N	2025-11-13 08:45:45	\N	\N	2025-11-13 08:45:45	2025-11-13 08:45:45
3	subscriber3@example.com	Bob Johnson	\N	active	manual	["customer","vip"]	\N	2025-11-13 08:45:45	\N	\N	2025-11-13 08:45:45	2025-11-13 08:45:45
\.


--
-- Data for Name: email_templates; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.email_templates (id, name, subject, content, plain_content, thumbnail, category, is_active, variables, created_at, updated_at) FROM stdin;
2	Monthly Newsletter	Newsletter Bulanan - {{month}} {{year}}	\n                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">\n                        <div style="background: linear-gradient(to right, #2563eb, #7c3aed); padding: 30px; text-align: center;">\n                            <h1 style="color: white; margin: 0;">Bizmark.ID Newsletter</h1>\n                            <p style="color: #e0e7ff; margin: 5px 0;">{{month}} {{year}}</p>\n                        </div>\n                        <div style="padding: 30px; background: #f8fafc;">\n                            <h2 style="color: #1e293b;">Halo {{name}}!</h2>\n                            <p>Berikut update penting bulan ini:</p>\n                            \n                            <div style="background: white; padding: 20px; border-radius: 8px; margin: 15px 0;">\n                                <h3 style="color: #2563eb;">📋 Peraturan Baru</h3>\n                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\n                            </div>\n                            \n                            <div style="background: white; padding: 20px; border-radius: 8px; margin: 15px 0;">\n                                <h3 style="color: #2563eb;">💡 Tips Perizinan</h3>\n                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\n                            </div>\n                            \n                            <div style="text-align: center; margin: 30px 0;">\n                                <a href="https://bizmark.id" style="background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;">\n                                    Kunjungi Website\n                                </a>\n                            </div>\n                        </div>\n                        <div style="background: #1e293b; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;">\n                            <p>© 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri</p>\n                            <p><a href="{{unsubscribe_url}}" style="color: #60a5fa;">Unsubscribe</a></p>\n                        </div>\n                    </div>\n                	\N	\N	newsletter	t	["name","email","month","year","unsubscribe_url"]	2025-11-13 08:45:45	2025-11-13 08:45:45
3	Promotion Email	🎉 Promo Spesial - Diskon Konsultasi Perizinan!	\n                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #fff;">\n                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;">\n                            <h1 style="color: white; font-size: 32px; margin: 0;">🎉 PROMO SPESIAL!</h1>\n                            <p style="color: #e0e7ff; font-size: 18px; margin: 10px 0;">Diskon hingga 30% Konsultasi Perizinan</p>\n                        </div>\n                        <div style="padding: 40px;">\n                            <p style="font-size: 16px; color: #333;">Halo {{name}},</p>\n                            <p>Kami punya kabar gembira untuk Anda!</p>\n                            \n                            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; margin: 20px 0;">\n                                <h3 style="color: #92400e; margin: 0 0 10px 0;">Diskon 30% untuk layanan:</h3>\n                                <ul style="color: #92400e; margin: 0;">\n                                    <li>Konsultasi Perizinan Lingkungan</li>\n                                    <li>Pengurusan IMB & PBG</li>\n                                    <li>Perizinan Operasional</li>\n                                </ul>\n                            </div>\n                            \n                            <p>Promo berlaku sampai <strong>31 Desember 2025</strong></p>\n                            \n                            <div style="text-align: center; margin: 30px 0;">\n                                <a href="https://wa.me/6283879602855?text=Saya%20tertarik%20dengan%20promo%20konsultasi" \n                                   style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; font-size: 16px;">\n                                    Hubungi Kami Sekarang\n                                </a>\n                            </div>\n                            \n                            <p style="color: #666; font-size: 14px; text-align: center;">Atau hubungi: <strong>+62 838 7960 2855</strong></p>\n                        </div>\n                        <div style="background: #f3f4f6; padding: 20px; text-align: center;">\n                            <p style="color: #6b7280; font-size: 12px; margin: 0;">\n                                © 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri<br>\n                                <a href="{{unsubscribe_url}}" style="color: #9ca3af;">Unsubscribe</a>\n                            </p>\n                        </div>\n                    </div>\n                	\N	\N	promotional	t	["name","email","unsubscribe_url"]	2025-11-13 08:45:45	2025-11-13 08:45:45
1	Welcome Email	Selamat Datang di Bizmark.ID!	<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">\r\n                        <h1 style="color: #2563eb;">Selamat Datang, {{name}}!</h1>\r\n                        <p>Terima kasih telah berlangganan newsletter Bizmark.ID.</p>\r\n                        <p>Kami akan mengirimkan update terbaru tentang:</p>\r\n                        <ul>\r\n                            <li>Peraturan perizinan terbaru</li>\r\n                            <li>Tips & trik pengurusan izin</li>\r\n                            <li>Promosi layanan konsultasi</li>\r\n                        </ul>\r\n                        <p>Jika ada pertanyaan, jangan ragu untuk menghubungi kami.</p>\r\n                        <p>Salam hangat,<br><strong>Tim Bizmark.ID</strong></p>\r\n                    </div>	\N	\N	transactional	t	["name","email","unsubscribe_url"]	2025-11-13 08:45:45	2025-11-13 10:46:17
\.


--
-- Data for Name: expense_categories; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.expense_categories (id, slug, name, "group", icon, is_default, is_active, sort_order, created_at, updated_at) FROM stdin;
1	personnel	Gaji & Honor	SDM & Personel	briefcase	t	t	10	2025-11-03 12:33:13	2025-11-03 12:33:13
2	commission	Komisi	SDM & Personel	handshake	t	t	20	2025-11-03 12:33:13	2025-11-03 12:33:13
3	allowance	Tunjangan & Bonus	SDM & Personel	money-bill-wave	t	t	30	2025-11-03 12:33:13	2025-11-03 12:33:13
4	subcontractor	Subkontraktor	Rekanan & Subkontraktor	hard-hat	t	t	40	2025-11-03 12:33:13	2025-11-03 12:33:13
5	consultant	Konsultan Eksternal	Rekanan & Subkontraktor	user-tie	t	t	50	2025-11-03 12:33:13	2025-11-03 12:33:13
6	supplier	Rekanan/Partner	Rekanan & Subkontraktor	handshake	t	t	60	2025-11-03 12:33:13	2025-11-03 12:33:13
7	laboratory	Laboratorium	Layanan Teknis	microscope	t	t	70	2025-11-03 12:33:13	2025-11-03 12:33:13
8	survey	Survey & Pengukuran	Layanan Teknis	ruler-combined	t	t	80	2025-11-03 12:33:13	2025-11-03 12:33:13
9	testing	Testing & Inspeksi	Layanan Teknis	vial	t	t	90	2025-11-03 12:33:13	2025-11-03 12:33:13
10	certification	Sertifikasi	Layanan Teknis	certificate	t	t	100	2025-11-03 12:33:13	2025-11-03 12:33:13
11	equipment_rental	Sewa Alat	Peralatan & Perlengkapan	truck-moving	t	t	110	2025-11-03 12:33:13	2025-11-03 12:33:13
12	equipment_purchase	Pembelian Alat	Peralatan & Perlengkapan	tools	t	t	120	2025-11-03 12:33:13	2025-11-03 12:33:13
13	materials	Perlengkapan & Supplies	Peralatan & Perlengkapan	box	t	t	130	2025-11-03 12:33:13	2025-11-03 12:33:13
14	maintenance	Maintenance & Perbaikan	Peralatan & Perlengkapan	wrench	t	t	140	2025-11-03 12:33:13	2025-11-03 12:33:13
15	travel	Perjalanan Dinas	Operasional	plane	t	t	150	2025-11-03 12:33:13	2025-11-03 12:33:13
16	accommodation	Akomodasi	Operasional	hotel	t	t	160	2025-11-03 12:33:13	2025-11-03 12:33:13
17	transportation	Transportasi	Operasional	car	t	t	170	2025-11-03 12:33:13	2025-11-03 12:33:13
18	communication	Komunikasi & Internet	Operasional	phone	t	t	180	2025-11-03 12:33:13	2025-11-03 12:33:13
19	office_supplies	ATK & Supplies	Operasional	file-alt	t	t	190	2025-11-03 12:33:13	2025-11-03 12:33:13
20	printing	Printing & Dokumen	Operasional	print	t	t	200	2025-11-03 12:33:13	2025-11-03 12:33:13
21	permit	Perizinan	Legal & Administrasi	file-contract	t	t	210	2025-11-03 12:33:13	2025-11-03 12:33:13
22	insurance	Asuransi	Legal & Administrasi	shield-alt	t	t	220	2025-11-03 12:33:13	2025-11-03 12:33:13
23	tax	Pajak & Retribusi	Legal & Administrasi	dollar-sign	t	t	230	2025-11-03 12:33:13	2025-11-03 12:33:13
24	legal	Legal & Notaris	Legal & Administrasi	balance-scale	t	t	240	2025-11-03 12:33:13	2025-11-03 12:33:13
25	administration	Administrasi	Legal & Administrasi	clipboard-list	t	t	250	2025-11-03 12:33:13	2025-11-03 12:33:13
26	marketing	Marketing & Promosi	Marketing & Lainnya	bullhorn	t	t	260	2025-11-03 12:33:13	2025-11-03 12:33:13
27	entertainment	Entertainment & Jamuan	Marketing & Lainnya	utensils	t	t	270	2025-11-03 12:33:13	2025-11-03 12:33:13
28	donation	Donasi & CSR	Marketing & Lainnya	gift	t	t	280	2025-11-03 12:33:13	2025-11-03 12:33:13
29	other	Lainnya	Marketing & Lainnya	ellipsis-h	t	t	999	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
1	8c3e82c3-7143-43ca-8924-33a974571e0e	database	default	{"uuid":"8c3e82c3-7143-43ca-8924-33a974571e0e","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:3;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";s:4:\\"1000\\";s:13:\\"building_area\\";s:3:\\"500\\";s:16:\\"land_certificate\\";s:8:\\"SHM No 1\\";s:13:\\"business_type\\";s:8:\\"Katering\\";s:8:\\"province\\";s:10:\\"Jawa barat\\";s:7:\\"regency\\";s:8:\\"Karawang\\";s:8:\\"district\\";s:14:\\"Karawang Barat\\";s:11:\\"institution\\";s:3:\\"BPN\\";}}"},"createdAt":1762177446,"delay":null}	PDOException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:411\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): PDOStatement->execute()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(811): Illuminate\\Database\\Connection->{closure:Illuminate\\Database\\Connection::select():397}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#17 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#42 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#44 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#45 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#47 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#48 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#49 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#50 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#51 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#52 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ (Connection: pgsql, SQL: insert into "a_i_processing_logs" ("template_id", "project_id", "operation_type", "status", "initiated_by", "metadata", "updated_at", "created_at") values (3, 1, paraphrase, processing, 1, {"started_at":"2025-11-03T13:44:07+00:00"}, 2025-11-03 13:44:07, 2025-11-03 13:44:07) returning "id") in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:824\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#15 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#42 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#44 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#45 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#47 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#48 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#49 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#50 {main}	2025-11-03 13:44:07
2	bb61af65-3580-44b4-b0a1-b5fe458759f0	database	default	{"uuid":"bb61af65-3580-44b4-b0a1-b5fe458759f0","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:3;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";N;s:13:\\"building_area\\";N;s:16:\\"land_certificate\\";N;s:13:\\"business_type\\";N;s:8:\\"province\\";N;s:7:\\"regency\\";N;s:8:\\"district\\";N;s:11:\\"institution\\";N;}}"},"createdAt":1762177517,"delay":null}	PDOException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:411\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): PDOStatement->execute()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(811): Illuminate\\Database\\Connection->{closure:Illuminate\\Database\\Connection::select():397}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#17 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#42 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#44 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#45 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#47 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#48 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#49 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#50 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#51 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#52 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ (Connection: pgsql, SQL: insert into "a_i_processing_logs" ("template_id", "project_id", "operation_type", "status", "initiated_by", "metadata", "updated_at", "created_at") values (3, 1, paraphrase, processing, 1, {"started_at":"2025-11-03T13:45:19+00:00"}, 2025-11-03 13:45:19, 2025-11-03 13:45:19) returning "id") in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:824\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#15 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#42 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#44 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#45 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#47 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#48 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#49 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#50 {main}	2025-11-03 13:45:19
3	b6031ac4-d9d9-4e46-af01-b46af0a605ae	database	default	{"uuid":"b6031ac4-d9d9-4e46-af01-b46af0a605ae","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:3;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";N;s:13:\\"building_area\\";N;s:16:\\"land_certificate\\";N;s:13:\\"business_type\\";N;s:8:\\"province\\";N;s:7:\\"regency\\";N;s:8:\\"district\\";N;s:11:\\"institution\\";N;}}"},"createdAt":1762177736,"delay":null}	PDOException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:411\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): PDOStatement->execute()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(811): Illuminate\\Database\\Connection->{closure:Illuminate\\Database\\Connection::select():397}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#17 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#42 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#44 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#45 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#47 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#48 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#49 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#50 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#51 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#52 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ (Connection: pgsql, SQL: insert into "a_i_processing_logs" ("template_id", "project_id", "operation_type", "status", "initiated_by", "metadata", "updated_at", "created_at") values (3, 1, paraphrase, processing, 1, {"started_at":"2025-11-03T13:48:58+00:00"}, 2025-11-03 13:48:58, 2025-11-03 13:48:58) returning "id") in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:824\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#15 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#42 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#44 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#45 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#47 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#48 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#49 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#50 {main}	2025-11-03 13:48:58
4	85af0ef8-4699-4b5b-b8c0-f5d568d7644b	database	default	{"uuid":"85af0ef8-4699-4b5b-b8c0-f5d568d7644b","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";N;s:13:\\"building_area\\";N;s:16:\\"land_certificate\\";N;s:13:\\"business_type\\";N;s:8:\\"province\\";N;s:7:\\"regency\\";N;s:8:\\"district\\";N;s:11:\\"institution\\";N;}}"},"createdAt":1762177752,"delay":null}	PDOException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:411\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): PDOStatement->execute()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(811): Illuminate\\Database\\Connection->{closure:Illuminate\\Database\\Connection::select():397}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#17 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#42 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#44 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#45 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#47 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#48 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#49 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#50 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#51 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#52 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ (Connection: pgsql, SQL: insert into "a_i_processing_logs" ("template_id", "project_id", "operation_type", "status", "initiated_by", "metadata", "updated_at", "created_at") values (1, 1, paraphrase, processing, 1, {"started_at":"2025-11-03T13:49:13+00:00"}, 2025-11-03 13:49:13, 2025-11-03 13:49:13) returning "id") in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:824\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#15 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#42 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#44 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#45 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#47 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#48 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#49 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#50 {main}	2025-11-03 13:49:13
5	5931e672-1429-411e-91b2-c5b157dc5e03	database	default	{"uuid":"5931e672-1429-411e-91b2-c5b157dc5e03","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:0:{}}"},"createdAt":1762177977,"delay":null}	PDOException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:411\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(411): PDOStatement->execute()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(811): Illuminate\\Database\\Connection->{closure:Illuminate\\Database\\Connection::select():397}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#17 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#42 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#44 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#45 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#47 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#48 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#49 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#50 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#51 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#52 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[42P01]: Undefined table: 7 ERROR:  relation "a_i_processing_logs" does not exist\nLINE 1: insert into "a_i_processing_logs" ("template_id", "project_i...\n                    ^ (Connection: pgsql, SQL: insert into "a_i_processing_logs" ("template_id", "project_id", "operation_type", "status", "initiated_by", "metadata", "updated_at", "created_at") values (1, 1, paraphrase, processing, 1, {"started_at":"2025-11-03T13:52:58+00:00"}, 2025-11-03 13:52:58, 2025-11-03 13:52:58) returning "id") in /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php:824\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(778): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(397): Illuminate\\Database\\Connection->run()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Connection.php(384): Illuminate\\Database\\Connection->select()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Processors/PostgresProcessor.php(24): Illuminate\\Database\\Connection->selectFromWriteConnection()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3853): Illuminate\\Database\\Query\\Processors\\PostgresProcessor->processInsertGetId()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(2235): Illuminate\\Database\\Query\\Builder->insertGetId()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1436): Illuminate\\Database\\Eloquent\\Builder->__call()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1401): Illuminate\\Database\\Eloquent\\Model->insertAndSetId()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1240): Illuminate\\Database\\Eloquent\\Model->performInsert()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1219): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/helpers.php(390): Illuminate\\Database\\Eloquent\\Builder->{closure:Illuminate\\Database\\Eloquent\\Builder::create():1218}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1218): tap()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Support/Traits/ForwardsCalls.php(23): Illuminate\\Database\\Eloquent\\Builder->create()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2540): Illuminate\\Database\\Eloquent\\Model->forwardCallTo()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(2556): Illuminate\\Database\\Eloquent\\Model->__call()\n#15 /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php(44): Illuminate\\Database\\Eloquent\\Model::__callStatic()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#26 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#28 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#29 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#30 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#33 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#34 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#35 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#36 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#37 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#38 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#39 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#40 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#41 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#42 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#43 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#44 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#45 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#46 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#47 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#48 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#49 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#50 {main}	2025-11-03 13:52:58
6	9dc26e54-2d8d-491c-acb6-66870c4fbb72	database	default	{"uuid":"9dc26e54-2d8d-491c-acb6-66870c4fbb72","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:1:{s:4:\\"test\\";s:4:\\"data\\";}}"},"createdAt":1762178034,"delay":null}	Exception: Missing required fields: client_name, location, land_area, land_certificate, pic_name in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:82\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 13:53:56
7	e9330824-c068-4ad8-a9ad-b9525c5a4916	database	default	{"uuid":"e9330824-c068-4ad8-a9ad-b9525c5a4916","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";N;s:13:\\"building_area\\";N;s:16:\\"land_certificate\\";N;s:13:\\"business_type\\";N;s:8:\\"province\\";N;s:7:\\"regency\\";N;s:8:\\"district\\";N;s:11:\\"institution\\";N;}}"},"createdAt":1762178162,"delay":null}	Exception: Missing required fields: client_name, location, land_area, land_certificate, pic_name in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:82\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 13:56:02
8	5cac8d04-a48c-4ebd-a6b3-50b37be91e3c	database	default	{"uuid":"5cac8d04-a48c-4ebd-a6b3-50b37be91e3c","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:3;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";s:4:\\"1000\\";s:13:\\"building_area\\";s:3:\\"500\\";s:16:\\"land_certificate\\";s:7:\\"SHm 123\\";s:13:\\"business_type\\";s:8:\\"Katering\\";s:8:\\"province\\";s:10:\\"Jawa Barat\\";s:7:\\"regency\\";s:13:\\"Kab. Karawang\\";s:8:\\"district\\";s:14:\\"Karawang Barat\\";s:11:\\"institution\\";s:3:\\"BPN\\";}}"},"createdAt":1762178202,"delay":null}	Exception: Missing required fields: client_name, location in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:82\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 13:56:44
9	37cf7e7c-a96d-47dd-96b8-db76388a3cd1	database	default	{"uuid":"37cf7e7c-a96d-47dd-96b8-db76388a3cd1","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:0:{}}"},"createdAt":1762179054,"delay":null}	Exception: AI paraphrasing failed: Invalid JSON response from OpenRouter in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:92\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 14:10:55
10	ea2a242f-8765-4bf8-9cd6-286a9425851d	database	default	{"uuid":"ea2a242f-8765-4bf8-9cd6-286a9425851d","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:0:{}}"},"createdAt":1762179158,"delay":null}	Exception: AI paraphrasing failed: Invalid JSON response from OpenRouter in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:92\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 14:12:40
11	977bb8be-7686-4102-a299-4dc9d3801266	database	default	{"uuid":"977bb8be-7686-4102-a299-4dc9d3801266","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:0:{}}"},"createdAt":1762179163,"delay":null}	Exception: AI paraphrasing failed: Invalid JSON response from OpenRouter in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:92\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 14:12:44
12	f9118cde-2ebb-408c-b1b2-b099ec113215	database	default	{"uuid":"f9118cde-2ebb-408c-b1b2-b099ec113215","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:0:{}}"},"createdAt":1762179246,"delay":null}	Exception: AI paraphrasing failed: Invalid JSON response from OpenRouter in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:92\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 14:14:09
13	90489028-cd33-4fec-aed9-e6a73027648a	database	default	{"uuid":"90489028-cd33-4fec-aed9-e6a73027648a","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:1;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:0:{}}"},"createdAt":1762179257,"delay":null}	Exception: AI paraphrasing failed: Invalid JSON response from OpenRouter in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:92\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 14:14:19
14	f64f12be-0226-4aee-a6da-9f157d2124a3	database	default	{"uuid":"f64f12be-0226-4aee-a6da-9f157d2124a3","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:3;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";N;s:13:\\"building_area\\";N;s:16:\\"land_certificate\\";N;s:13:\\"business_type\\";N;s:8:\\"province\\";N;s:7:\\"regency\\";N;s:8:\\"district\\";N;s:11:\\"institution\\";N;}}"},"createdAt":1762187208,"delay":null}	Exception: Missing required fields: building_area, land_certificate in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:82\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 16:26:50
15	be56d245-c7c0-4680-91f7-df782a420f5b	database	default	{"uuid":"be56d245-c7c0-4680-91f7-df782a420f5b","displayName":"App\\\\Jobs\\\\ParaphraseDocumentJob","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":3,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":600,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\ParaphraseDocumentJob","command":"O:30:\\"App\\\\Jobs\\\\ParaphraseDocumentJob\\":4:{s:9:\\"projectId\\";i:1;s:10:\\"templateId\\";i:2;s:6:\\"userId\\";i:1;s:17:\\"additionalContext\\";a:8:{s:9:\\"land_area\\";N;s:13:\\"building_area\\";N;s:16:\\"land_certificate\\";N;s:13:\\"business_type\\";N;s:8:\\"province\\";N;s:7:\\"regency\\";N;s:8:\\"district\\";N;s:11:\\"institution\\";N;}}"},"createdAt":1762190685,"delay":null}	Exception: Missing required fields: business_type, land_area, building_area in /root/Bizmark.id/app/Jobs/ParaphraseDocumentJob.php:82\nStack trace:\n#0 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ParaphraseDocumentJob->handle()\n#1 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#2 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#5 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Container\\Container->call()\n#6 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->{closure:Illuminate\\Bus\\Dispatcher::dispatchNow():129}()\n#7 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#8 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(136): Illuminate\\Pipeline\\Pipeline->then()\n#9 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->{closure:Illuminate\\Queue\\CallQueuedHandler::dispatchThroughMiddleware():127}()\n#11 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}()\n#12 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#17 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(187): Illuminate\\Queue\\Worker->runJob()\n#18 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#22 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#25 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#26 /root/Bizmark.id/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#27 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 /root/Bizmark.id/vendor/symfony/console/Application.php(1110): Illuminate\\Console\\Command->run()\n#29 /root/Bizmark.id/vendor/symfony/console/Application.php(359): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 /root/Bizmark.id/vendor/symfony/console/Application.php(194): Symfony\\Component\\Console\\Application->doRun()\n#31 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#32 /root/Bizmark.id/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 /root/Bizmark.id/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}	2025-11-03 17:24:46
\.


--
-- Data for Name: institutions; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.institutions (id, name, type, address, phone, email, contact_person, contact_position, notes, is_active, created_at, updated_at) FROM stdin;
1	Dinas Lingkungan Hidup Provinsi	DLH	Jl. Lingkungan Hidup No. 1	021-1234567	dlh@provinsi.go.id	Dr. Ahmad Hidayat	Kepala Bidang Perizinan	Untuk izin lingkungan hidup dan AMDAL	t	2025-11-03 12:33:13	2025-11-03 12:33:13
2	Badan Pertanahan Nasional	BPN	Jl. Pertanahan No. 2	021-2345678	bpn@go.id	Ir. Siti Nurhaliza	Kepala Seksi Hak Tanah	Untuk sertifikat tanah dan Pertek	t	2025-11-03 12:33:13	2025-11-03 12:33:13
3	Online Single Submission (OSS)	OSS	Portal Digital	1500-033	support@oss.go.id	Call Center OSS	Customer Service	Perizinan berusaha online	t	2025-11-03 12:33:13	2025-11-03 12:33:13
4	Notaris Andri Wijaya, S.H., M.Kn	Notaris	Jl. Hukum No. 3, Jakarta	021-3456789	notaris.andri@example.com	Andri Wijaya	Notaris	Untuk akta notaris dan pendirian badan usaha	t	2025-11-03 12:33:13	2025-11-03 12:33:13
5	Dinas Perhubungan Provinsi	DISHUB	Jl. Transportasi No. 4	021-4567890	dishub@provinsi.go.id	Drs. Bambang Sutrisno	Kepala Bidang Lalu Lintas	Untuk izin andalalin (analisis dampak lalu lintas)	t	2025-11-03 12:33:13	2025-11-03 12:33:13
6	Dinas Kesehatan Provinsi	DINKES	Jl. Kesehatan No. 5	021-5678901	dinkes@provinsi.go.id	dr. Maya Sari, M.Kes	Kepala Bidang Perizinan Kesehatan	Untuk izin rumah sakit dan fasilitas kesehatan	t	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: invoice_items; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.invoice_items (id, invoice_id, description, quantity, unit_price, amount, "order", created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: invoices; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.invoices (id, project_id, invoice_number, invoice_date, due_date, subtotal, tax_rate, tax_amount, total_amount, paid_amount, remaining_amount, status, notes, client_name, client_address, client_tax_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: job_applications; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.job_applications (id, job_vacancy_id, full_name, email, phone, birth_date, gender, address, education_level, major, institution, graduation_year, gpa, work_experience, has_experience_ukl_upl, skills, cv_path, portfolio_path, cover_letter, expected_salary, available_from, status, notes, reviewed_at, reviewed_by, created_at, updated_at) FROM stdin;
1	1	Odang Rodiana	studiomalaka@gmail.com	081382605030	2001-01-13	Pria	Grand Permata B3/7 Palumbonsari	S1	Psikologi	UBP	2024	3.50	[]	f	[]	applications/cv/JTYFiPaSR9EaaQLmV5MVkZni4GDg3nFGmloz0XfR.pdf	\N	\N	50000000	2025-11-30	pending	\N	\N	\N	2025-11-13 07:07:17	2025-11-13 07:07:17
2	1	Arini Salma Hanifah	ccarini1998@gmail.com	081321437256	1998-06-24	Wanita	Kp. Kalihurip RT05/02 No. 60, Desa Duren, Kec. Klari, Kab. Karawang 41371 (orang tua) \r\nPurwasari Indah 2 Blok B2 NO. 16, Purwasari, Kab. Karawang 41373 (pribadi)	S1	Sastra Indonesia	Universitas Padjadjaran	2019	3.55	[{"company":"PT. Multi Nitrotama Kimia","position":"Corporate Strategy","duration":"Jul 2022 - Aug 2025","responsibilities":"Develop and implement corporate strategies to support the company\\u2019s long-term vision,\\nincluding business growth across MNK and its subsidiaries.\\nConduct market research and competitive analysis to identify opportunities, risks, and industry\\ntrends relevant to MNK group companies.\\nCollaborate with cross-functional teams and subsidiaries to align operational plans with overall\\ncorporate goals.\\nMonitor and evaluate the performance of strategic initiatives, ensuring alignment with business\\ntargets at both parent and subsidiary levels."}]	t	[]	applications/cv/S0cEf960ahyYPESIPPAq9yrBLMibNGV9aYohWwL3.pdf	\N	Saya memiliki pengalaman dan ketertarikan yang kuat di bidang penulisan teknis, terutama yang berkaitan dengan isu lingkungan, industri, dan keberlanjutan. Selama bekerja di PT. Multi Kimia Solusindo, anak perusahaan dari PT. Multi Nitrotama Kimia, saya terbiasa dengan proses-proses industri serta memahami karakteristik bahan kimia dan dampaknya terhadap lingkungan. Pengalaman tersebut membuat saya memiliki dasar teknis yang kuat dalam menyusun dokumen lingkungan yang akurat, aplikatif, dan sesuai dengan peraturan yang berlaku.\r\n\r\nSelain kemampuan teknis, saya memiliki latar belakang yang kuat dalam penulisan dan analisis dokumen. Saya terbiasa menyusun laporan, proposal, dan kajian ilmiah dengan struktur logis, bahasa yang jelas, dan perhatian besar pada detail. Bagi saya, drafting bukan hanya soal menulis data, tetapi juga bagaimana memastikan setiap informasi dapat dipertanggungjawabkan secara ilmiah dan komunikatif.\r\n\r\nSaya juga aktif menulis tentang isu lingkungan dan sosial di berbagai platform literasi dan seni yang saya kelola. Hal ini membentuk cara pandang saya terhadap pentingnya integrasi antara sains, kebijakan, dan kepedulian terhadap manusia serta alam. Pendekatan tersebut membantu saya menulis dokumen lingkungan yang tidak hanya memenuhi aspek regulatif, tapi juga mencerminkan nilai-nilai keberlanjutan dan tanggung jawab sosial.\r\n\r\nDengan kombinasi pemahaman teknis, ketelitian administratif, serta kepekaan sosial-ekologis, saya percaya bahwa saya dapat berkontribusi secara nyata dalam menyusun dokumen lingkungan dan teknis yang tidak hanya sesuai dengan standar, tetapi juga berorientasi pada keberlanjutan jangka panjang.	7000000	2026-01-25	pending	\N	\N	\N	2025-11-13 08:07:49	2025-11-13 08:07:49
3	1	Odang Rodiana	tanerizawa@gmail.com	081382605030	2008-01-01	Pria	Grand Permata B3/7 Palumbonsari	S2	Psikologi	UBP	2021	3.50	[]	t	[]	applications/cv/J8ftxqZN0s5nYmEVhBWTQbDjMQP14rNzhdyHErgJ.pdf	\N	\N	5000000	2025-11-30	pending	\N	\N	\N	2025-11-13 10:03:29	2025-11-13 10:03:29
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: job_vacancies; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.job_vacancies (id, title, slug, "position", description, responsibilities, qualifications, benefits, employment_type, location, salary_min, salary_max, salary_negotiable, deadline, status, google_form_url, applications_count, created_at, updated_at, deleted_at) FROM stdin;
1	Drafter Dokumen Lingkungan & Teknis	drafter-dokumen-lingkungan-teknis	Drafter Dokumen Teknis & Lingkungan	PT. Cangah Pajaratan Mandiri (Bizmark.ID) adalah perusahaan konsultan yang bergerak di bidang perizinan, dokumen lingkungan, dan kajian teknis. Kantor kami berlokasi di Karawang, Jawa Barat.\n\nKami mencari individu yang teliti, komunikatif, dan mampu bekerja dalam tim untuk bergabung sebagai Drafter Dokumen Lingkungan & Teknis.\n\nPosisi ini menawarkan fleksibilitas kerja REMOTE dengan sistem work from home, namun terkadang memerlukan kehadiran untuk meeting offline dan koordinasi tim di kantor Karawang. Oleh karena itu, kandidat WAJIB memiliki kendaraan motor (SIM C) dan laptop pribadi.\n\nKami memberikan kesempatan bagi lulusan baru maupun profesional yang ingin mengembangkan karir di bidang penyusunan dokumen teknis dan lingkungan, dengan proyek yang variatif dan menantang.	["Menyusun dokumen UKL-UPL, Kajian Lingkungan, dan Pertimbangan Teknis","Mengumpulkan data lapangan dan melakukan pengolahan data teknis","Menyusun laporan teknis dengan format yang rapi dan terstruktur","Bekerja sama dengan tim teknis dan analis dalam penyusunan dokumen","Memastikan kualitas format, layout, dan struktur dokumen sesuai standar","Melakukan revisi dokumen berdasarkan feedback klien atau konsultan senior"]	["Pendidikan minimal D3\\/S1 dari jurusan Teknik Lingkungan, Teknik Sipil, Planologi, atau bidang terkait","Terampil dalam pengolahan dan drafting dokumen teknis","Mampu menggunakan Microsoft Office (Word, Excel, PowerPoint) dengan baik","Diutamakan memiliki pengalaman dalam menyusun dokumen UKL-UPL atau Kajian Teknis","WAJIB memiliki kendaraan motor dan SIM C (untuk mobilitas meeting offline)","WAJIB memiliki laptop pribadi dengan spesifikasi memadai untuk pekerjaan remote","Teliti, komunikatif, dan mampu bekerja sesuai deadline","Mampu bekerja secara individu maupun dalam tim","Fresh graduate dipersilakan melamar"]	["Fleksibilitas kerja REMOTE (Work From Home)","Meeting offline sesekali untuk koordinasi tim di kantor Karawang","Tunjangan transportasi untuk meeting offline","Lingkungan kerja profesional dengan tim yang suportif","Proyek yang variatif di bidang teknis dan lingkungan","Kesempatan untuk meningkatkan kompetensi dalam penyusunan dokumen teknis","Pelatihan dan bimbingan dari konsultan senior","Gaji kompetitif sesuai pengalaman","Jenjang karir yang jelas","Kesempatan berkembang di industri konsultan lingkungan"]	remote	Karawang, Jawa Barat	\N	\N	t	2025-11-20	open	\N	3	2025-11-13 07:03:26	2025-11-13 10:03:29	\N
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_10_01_154236_create_institutions_table	1
5	2025_10_01_154240_create_project_statuses_table	1
6	2025_10_01_154249_create_projects_table	1
7	2025_10_01_154250_create_tasks_table	1
8	2025_10_01_154251_create_documents_table	1
9	2025_10_01_154252_create_project_logs_table	1
10	2025_10_01_154525_modify_users_table_for_perizinan	1
11	2025_10_01_162642_update_projects_table_structure	1
12	2025_10_01_162947_update_project_logs_user_id_nullable	1
13	2025_10_02_083808_add_financial_columns_to_projects_table	1
14	2025_10_02_083820_create_cash_accounts_table	1
15	2025_10_02_083827_create_project_payments_table	1
16	2025_10_02_083833_create_project_expenses_table	1
17	2025_10_02_091228_fix_payment_method_enum_values	1
18	2025_10_02_094020_create_permit_types_table	1
19	2025_10_02_094048_create_permit_templates_table	1
20	2025_10_02_094049_create_permit_template_items_table	1
21	2025_10_02_094050_create_permit_template_dependencies_table	1
22	2025_10_02_094102_create_project_permits_table	1
23	2025_10_02_094103_create_project_permit_dependencies_table	1
24	2025_10_02_155735_create_invoices_table	1
25	2025_10_02_155743_create_invoice_items_table	1
26	2025_10_02_155750_create_payment_schedules_table	1
27	2025_10_02_211614_create_permit_documents_table	1
28	2025_10_03_140559_add_permit_id_to_tasks_table	1
29	2025_10_03_163452_create_clients_table	1
30	2025_10_03_163524_add_client_id_to_projects_table	1
31	2025_10_03_194740_add_receivable_fields_to_project_expenses_table	1
32	2025_10_03_200841_update_category_enum_in_project_expenses_table	1
33	2025_10_03_204402_update_payment_method_in_project_expenses_table	1
34	2025_10_03_223304_add_invoice_id_to_project_payments_table	1
35	2025_10_04_000000_add_override_fields_to_project_permits_table	1
36	2025_10_09_190642_make_client_fields_nullable_in_projects_table	1
37	2025_10_09_223659_create_roles_and_permissions_tables	1
38	2025_10_10_115340_create_milestones_table	1
39	2025_10_10_181053_create_bank_reconciliations_table	1
40	2025_10_10_181100_create_bank_statement_entries_table	1
41	2025_10_10_181107_add_reconciliation_columns_to_transactions	1
42	2025_10_10_201930_create_articles_table	1
43	2025_10_11_000000_add_role_id_to_users_table	1
44	2025_10_11_000100_create_system_settings_table	1
45	2025_10_11_000200_create_expense_categories_table	1
46	2025_10_11_000300_create_payment_methods_table	1
47	2025_10_11_000400_create_tax_rates_table	1
48	2025_10_11_000500_create_security_settings_table	1
49	2025_11_03_130752_create_document_templates_table	2
50	2025_11_03_130753_create_ai_processing_logs_table	2
51	2025_11_03_130754_create_document_drafts_table	2
52	2025_11_03_163009_add_soft_deletes_to_document_drafts_table	3
53	2025_11_03_170000_create_compliance_checks_table	4
54	2025_11_11_183329_add_auth_fields_to_clients_table	5
55	2025_11_13_064858_create_job_vacancies_table	6
56	2025_11_13_064910_create_job_applications_table	7
57	2025_11_13_072934_add_unique_email_per_job_to_job_applications	8
58	2025_11_13_081830_create_email_subscribers_table	9
59	2025_11_13_081832_create_email_templates_table	10
60	2025_11_13_081834_create_email_campaigns_table	11
61	2025_11_13_081836_create_email_logs_table	12
62	2025_11_13_081839_create_email_inbox_table	13
\.


--
-- Data for Name: milestones; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.milestones (id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: payment_methods; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.payment_methods (id, code, name, description, requires_cash_account, is_default, is_active, sort_order, created_at, updated_at) FROM stdin;
1	bank_transfer	Transfer Bank	Pembayaran melalui transfer antar bank.	t	t	t	10	2025-11-03 12:33:13	2025-11-03 12:33:13
2	cash	Kas Tunai	Pembayaran langsung menggunakan uang tunai.	t	f	t	20	2025-11-03 12:33:13	2025-11-03 12:33:13
3	check	Cek	Pembayaran menggunakan cek.	t	f	t	30	2025-11-03 12:33:13	2025-11-03 12:33:13
4	giro	Giro	Pembayaran menggunakan giro/bilyet.	t	f	t	40	2025-11-03 12:33:13	2025-11-03 12:33:13
5	other	Metode Lainnya	Metode pembayaran khusus sesuai kesepakatan.	f	f	t	100	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: payment_schedules; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.payment_schedules (id, project_id, invoice_id, description, amount, due_date, paid_date, status, payment_method, reference_number, notes, created_at, updated_at, is_reconciled, reconciled_at, reconciliation_id) FROM stdin;
\.


--
-- Data for Name: permission_role; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permission_role (id, role_id, permission_id, created_at, updated_at) FROM stdin;
1	1	1	\N	\N
2	1	2	\N	\N
3	1	3	\N	\N
4	1	4	\N	\N
5	1	5	\N	\N
6	1	6	\N	\N
7	1	7	\N	\N
8	1	8	\N	\N
9	1	9	\N	\N
10	1	10	\N	\N
11	1	11	\N	\N
12	1	12	\N	\N
13	1	13	\N	\N
14	1	14	\N	\N
15	1	15	\N	\N
16	1	16	\N	\N
17	1	17	\N	\N
18	1	18	\N	\N
19	1	19	\N	\N
20	1	20	\N	\N
21	1	21	\N	\N
22	1	22	\N	\N
23	1	23	\N	\N
24	1	24	\N	\N
25	1	25	\N	\N
26	1	26	\N	\N
27	1	27	\N	\N
28	1	28	\N	\N
29	1	29	\N	\N
30	1	30	\N	\N
31	1	31	\N	\N
32	1	32	\N	\N
33	2	1	\N	\N
34	2	2	\N	\N
35	2	3	\N	\N
36	2	4	\N	\N
37	2	5	\N	\N
38	2	6	\N	\N
39	2	7	\N	\N
40	2	8	\N	\N
41	2	9	\N	\N
42	2	10	\N	\N
43	2	11	\N	\N
44	2	12	\N	\N
45	2	13	\N	\N
46	2	19	\N	\N
47	2	20	\N	\N
48	2	21	\N	\N
49	2	22	\N	\N
50	2	23	\N	\N
51	2	24	\N	\N
52	2	25	\N	\N
53	2	26	\N	\N
54	2	14	\N	\N
55	2	18	\N	\N
56	3	9	\N	\N
57	3	10	\N	\N
58	3	11	\N	\N
59	3	12	\N	\N
60	3	13	\N	\N
61	3	14	\N	\N
62	3	15	\N	\N
63	3	16	\N	\N
64	3	17	\N	\N
65	3	18	\N	\N
66	3	1	\N	\N
67	3	5	\N	\N
68	4	1	\N	\N
69	4	19	\N	\N
70	4	21	\N	\N
71	4	24	\N	\N
72	4	25	\N	\N
73	5	1	\N	\N
74	5	5	\N	\N
75	5	9	\N	\N
76	5	14	\N	\N
77	5	19	\N	\N
78	5	24	\N	\N
79	5	27	\N	\N
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permissions (id, name, display_name, "group", description, created_at, updated_at) FROM stdin;
1	projects.view	View Projects	projects	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
2	projects.create	Create Projects	projects	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
3	projects.edit	Edit Projects	projects	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
4	projects.delete	Delete Projects	projects	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
5	clients.view	View Clients	clients	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
6	clients.create	Create Clients	clients	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
7	clients.edit	Edit Clients	clients	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
8	clients.delete	Delete Clients	clients	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
9	invoices.view	View Invoices	invoices	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
10	invoices.create	Create Invoices	invoices	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
11	invoices.edit	Edit Invoices	invoices	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
12	invoices.delete	Delete Invoices	invoices	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
13	invoices.approve	Approve Invoices	invoices	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
14	finances.view	View Finances	finances	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
15	finances.manage_payments	Manage Payments	finances	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
16	finances.manage_expenses	Manage Expenses	finances	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
17	finances.manage_accounts	Manage Cash Accounts	finances	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
18	finances.view_reports	View Financial Reports	finances	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
19	tasks.view	View Tasks	tasks	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
20	tasks.create	Create Tasks	tasks	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
21	tasks.edit	Edit Tasks	tasks	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
22	tasks.delete	Delete Tasks	tasks	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
23	tasks.assign	Assign Tasks	tasks	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
24	documents.view	View Documents	documents	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
25	documents.upload	Upload Documents	documents	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
26	documents.delete	Delete Documents	documents	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
27	users.view	View Users	users	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
28	users.create	Create Users	users	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
29	users.edit	Edit Users	users	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
30	users.delete	Delete Users	users	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
31	settings.manage	Manage Settings	settings	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
32	roles.manage	Manage Roles	settings	\N	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: permit_documents; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permit_documents (id, project_permit_id, filename, original_filename, file_path, file_type, file_size, description, uploaded_by, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permit_template_dependencies; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permit_template_dependencies (id, template_id, permit_item_id, depends_on_item_id, dependency_type, notes, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permit_template_items; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permit_template_items (id, template_id, permit_type_id, custom_permit_name, sequence_order, is_goal_permit, estimated_days, estimated_cost, notes, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permit_templates; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permit_templates (id, name, description, use_case, category, created_by_user_id, is_public, usage_count, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: permit_types; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.permit_types (id, name, code, category, institution_id, avg_processing_days, description, required_documents, estimated_cost_min, estimated_cost_max, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: project_expenses; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.project_expenses (id, project_id, expense_date, vendor_name, amount, bank_account_id, description, receipt_file, is_billable, created_by, created_at, updated_at, is_receivable, receivable_from, receivable_status, receivable_paid_amount, receivable_notes, category, payment_method, is_reconciled, reconciled_at, reconciliation_id) FROM stdin;
\.


--
-- Data for Name: project_logs; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.project_logs (id, project_id, user_id, action, entity_type, entity_id, description, old_values, new_values, notes, ip_address, user_agent, created_at, updated_at) FROM stdin;
1	1	\N	created	\N	\N	Proyek 'Perizinan IMB Gedung Perkantoran' berhasil dibuat	\N	{"id":1,"name":"Perizinan IMB Gedung Perkantoran","description":"Pengurusan Izin Mendirikan Bangunan untuk gedung perkantoran 8 lantai di kawasan bisnis Jakarta Selatan","client_name":"PT Mega Prima Development","client_address":"Jl. Sudirman No. 123, Jakarta Selatan","status_id":2,"institution_id":1,"notes":"Dokumen teknis sudah diserahkan. Menunggu verifikasi lapangan dari dinas.","created_at":"2025-11-03T12:33:14.000000Z","updated_at":"2025-11-03T12:33:14.000000Z","client_contact":"021-7654321 \\/ mega.prima@email.com","start_date":"2025-09-15T00:00:00.000000Z","deadline":"2025-12-15T00:00:00.000000Z","progress_percentage":65,"budget":"250000000.00","actual_cost":"150000000.00","contract_value":"0.00","down_payment":"0.00","payment_received":"0.00","total_expenses":"0.00","profit_margin":"0.00","payment_terms":null,"payment_status":"unpaid","client_id":null}	\N	\N	\N	2025-11-03 12:33:14	2025-11-03 12:33:14
2	1	\N	status_changed	\N	\N	Status proyek diubah menjadi 'Kontrak'	\N	{"status_id":2}	\N	\N	\N	2025-11-04 12:33:14	2025-11-03 12:33:14
3	2	\N	created	\N	\N	Proyek 'Perizinan UKL-UPL Pabrik Tekstil' berhasil dibuat	\N	{"id":2,"name":"Perizinan UKL-UPL Pabrik Tekstil","description":"Pengurusan perizinan lingkungan UKL-UPL untuk pabrik tekstil dengan kapasitas produksi 500 ton\\/bulan","client_name":"CV Tekstil Nusantara","client_address":"Kawasan Industri Cibitung, Bekasi","status_id":3,"institution_id":2,"notes":"Dokumen UKL-UPL telah diserahkan. Sedang dalam tahap review teknis.","created_at":"2025-11-03T12:33:14.000000Z","updated_at":"2025-11-03T12:33:14.000000Z","client_contact":"0251-8765432 \\/ tekstil.nusantara@email.com","start_date":"2025-08-01T00:00:00.000000Z","deadline":"2025-11-30T00:00:00.000000Z","progress_percentage":80,"budget":"175000000.00","actual_cost":"140000000.00","contract_value":"0.00","down_payment":"0.00","payment_received":"0.00","total_expenses":"0.00","profit_margin":"0.00","payment_terms":null,"payment_status":"unpaid","client_id":null}	\N	\N	\N	2025-11-03 12:33:14	2025-11-03 12:33:14
4	2	\N	status_changed	\N	\N	Status proyek diubah menjadi 'Pengumpulan Dokumen'	\N	{"status_id":3}	\N	\N	\N	2025-11-04 12:33:14	2025-11-03 12:33:14
5	3	\N	created	\N	\N	Proyek 'Andalalin Mall Shopping Center' berhasil dibuat	\N	{"id":3,"name":"Andalalin Mall Shopping Center","description":"Analisis Dampak Lalu Lintas untuk pembangunan mall dengan luas 15.000 m2","client_name":"PT Metropolitan Shopping","client_address":"Jl. Raya Bogor KM 25, Depok","status_id":1,"institution_id":3,"notes":"Survey traffic counting sudah dimulai. Koordinasi dengan Dishub untuk data sekunder.","created_at":"2025-11-03T12:33:14.000000Z","updated_at":"2025-11-03T12:33:14.000000Z","client_contact":"021-9876543 \\/ metro.shopping@email.com","start_date":"2025-10-01T00:00:00.000000Z","deadline":"2026-01-31T00:00:00.000000Z","progress_percentage":25,"budget":"300000000.00","actual_cost":"75000000.00","contract_value":"0.00","down_payment":"0.00","payment_received":"0.00","total_expenses":"0.00","profit_margin":"0.00","payment_terms":null,"payment_status":"unpaid","client_id":null}	\N	\N	\N	2025-11-03 12:33:14	2025-11-03 12:33:14
6	3	\N	status_changed	\N	\N	Status proyek diubah menjadi 'Penawaran'	\N	{"status_id":1}	\N	\N	\N	2025-11-04 12:33:14	2025-11-03 12:33:14
\.


--
-- Data for Name: project_payments; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.project_payments (id, project_id, payment_date, amount, payment_type, bank_account_id, reference_number, description, receipt_file, created_by, created_at, updated_at, payment_method, invoice_id, is_reconciled, reconciled_at, reconciliation_id) FROM stdin;
\.


--
-- Data for Name: project_permit_dependencies; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.project_permit_dependencies (id, project_permit_id, depends_on_permit_id, dependency_type, can_proceed_without, override_reason, override_document_path, overridden_by_user_id, overridden_at, created_by_user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: project_permits; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.project_permits (id, project_id, permit_type_id, custom_permit_name, custom_institution_name, sequence_order, is_goal_permit, status, has_existing_document, existing_document_id, assigned_to_user_id, started_at, submitted_at, approved_at, rejected_at, target_date, estimated_cost, actual_cost, permit_number, valid_until, notes, created_at, updated_at, override_dependencies, override_reason, override_by_user_id, override_at) FROM stdin;
\.


--
-- Data for Name: project_statuses; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.project_statuses (id, name, code, description, color, sort_order, is_active, is_final, created_at, updated_at) FROM stdin;
1	Penawaran	PENAWARAN	Tahap penawaran ke client	#3B82F6	1	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
2	Kontrak	KONTRAK	Kontrak telah ditandatangani	#10B981	2	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
3	Pengumpulan Dokumen	PENGUMPULAN_DOK	Pengumpulan dan penyusunan dokumen	#F59E0B	3	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
4	Proses di DLH	PROSES_DLH	Dokumen sedang diproses di Dinas Lingkungan Hidup	#8B5CF6	4	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
5	Proses di BPN	PROSES_BPN	Dokumen sedang diproses di BPN	#EC4899	5	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
6	Proses di OSS	PROSES_OSS	Dokumen sedang diproses di OSS	#06B6D4	6	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
7	Proses di Notaris	PROSES_NOTARIS	Dokumen sedang diproses di Notaris	#84CC16	7	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
8	Menunggu Persetujuan	MENUNGGU_PERSETUJUAN	Menunggu persetujuan dari instansi terkait	#F97316	8	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
9	SK Terbit	SK_TERBIT	Surat Keputusan izin telah terbit	#22C55E	9	t	t	2025-11-03 12:33:13	2025-11-03 12:33:13
10	Dibatalkan	DIBATALKAN	Proyek dibatalkan	#EF4444	10	t	t	2025-11-03 12:33:13	2025-11-03 12:33:13
11	Ditunda	DITUNDA	Proyek ditunda sementara	#6B7280	11	t	f	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.projects (id, name, description, client_name, client_address, status_id, institution_id, notes, created_at, updated_at, client_contact, start_date, deadline, progress_percentage, budget, actual_cost, contract_value, down_payment, payment_received, total_expenses, profit_margin, payment_terms, payment_status, client_id) FROM stdin;
1	Perizinan IMB Gedung Perkantoran	Pengurusan Izin Mendirikan Bangunan untuk gedung perkantoran 8 lantai di kawasan bisnis Jakarta Selatan	PT Mega Prima Development	Jl. Sudirman No. 123, Jakarta Selatan	2	1	Dokumen teknis sudah diserahkan. Menunggu verifikasi lapangan dari dinas.	2025-11-03 12:33:14	2025-11-03 12:33:14	021-7654321 / mega.prima@email.com	2025-09-15	2025-12-15	65	250000000.00	150000000.00	0.00	0.00	0.00	0.00	0.00	\N	unpaid	\N
2	Perizinan UKL-UPL Pabrik Tekstil	Pengurusan perizinan lingkungan UKL-UPL untuk pabrik tekstil dengan kapasitas produksi 500 ton/bulan	CV Tekstil Nusantara	Kawasan Industri Cibitung, Bekasi	3	2	Dokumen UKL-UPL telah diserahkan. Sedang dalam tahap review teknis.	2025-11-03 12:33:14	2025-11-03 12:33:14	0251-8765432 / tekstil.nusantara@email.com	2025-08-01	2025-11-30	80	175000000.00	140000000.00	0.00	0.00	0.00	0.00	0.00	\N	unpaid	\N
3	Andalalin Mall Shopping Center	Analisis Dampak Lalu Lintas untuk pembangunan mall dengan luas 15.000 m2	PT Metropolitan Shopping	Jl. Raya Bogor KM 25, Depok	1	3	Survey traffic counting sudah dimulai. Koordinasi dengan Dishub untuk data sekunder.	2025-11-03 12:33:14	2025-11-03 12:33:14	021-9876543 / metro.shopping@email.com	2025-10-01	2026-01-31	25	300000000.00	75000000.00	0.00	0.00	0.00	0.00	0.00	\N	unpaid	\N
4	Sertifikat Halal Produk Makanan	Pengurusan sertifikat halal untuk 15 varian produk makanan olahan	UD Berkah Mandiri	Jl. Malioboro No. 45, Yogyakarta	4	4	Sertifikat halal telah diterbitkan untuk semua produk. Proses selesai tepat waktu.	2025-11-03 12:33:14	2025-11-03 12:33:14	0274-654321 / berkah.mandiri@email.com	2025-07-01	2025-09-30	100	85000000.00	80000000.00	0.00	0.00	0.00	0.00	0.00	\N	unpaid	\N
5	Perizinan OSS Startup Teknologi	Pengurusan izin usaha melalui Online Single Submission untuk perusahaan teknologi finansial	PT Digital Inovasi Indonesia	BSD Green Office Park, Tangerang Selatan	2	5	Berkas NIB sudah disubmit. Menunggu persetujuan dari kementerian terkait.	2025-11-03 12:33:14	2025-11-03 12:33:14	021-5432167 / digital.inovasi@email.com	2025-09-20	2025-11-20	40	120000000.00	48000000.00	0.00	0.00	0.00	0.00	0.00	\N	unpaid	\N
6	KKPR Kawasan Industri	Kajian Keselamatan dan Kesehatan Kerja serta Perlindungan Radiasi untuk kawasan industri kimia	PT Industri Kimia Sejahtera	Kawasan Industri Cilegon, Banten	5	1	Proses ditunda karena menunggu hasil audit safety dari konsultan internasional.	2025-11-03 12:33:14	2025-11-03 12:33:14	021-7891234 / kimia.sejahtera@email.com	2025-08-15	2025-12-31	30	400000000.00	120000000.00	0.00	0.00	0.00	0.00	0.00	\N	unpaid	\N
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.roles (id, name, display_name, description, is_system, created_at, updated_at) FROM stdin;
1	admin	Administrator	Full access to all features	t	2025-11-03 12:33:13	2025-11-03 12:33:13
2	manager	Manager	Manage projects, clients, and team members	t	2025-11-03 12:33:13	2025-11-03 12:33:13
3	accountant	Accountant	Manage finances, invoices, and payments	t	2025-11-03 12:33:13	2025-11-03 12:33:13
4	staff	Staff	Basic access to projects and tasks	t	2025-11-03 12:33:13	2025-11-03 12:33:13
5	viewer	Viewer	Read-only access	t	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: security_settings; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.security_settings (id, min_password_length, require_special_char, require_number, require_mixed_case, enforce_password_expiration, password_expiration_days, session_timeout_minutes, allow_concurrent_sessions, two_factor_enabled, activity_log_enabled, created_at, updated_at) FROM stdin;
1	8	t	t	t	f	90	30	t	f	t	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
YkNfIAfCeFfuDEubuT4D2nokeW2qAeOmbYbDgO2G	\N	31.97.222.208	curl/8.14.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQThSSUJROHhCZHVZNE9BR2t0M3BkZlBSVWdaSWt3bFJYb0N2dzFVRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MToiaHR0cHM6Ly9iaXptYXJrLmlkL2FkbWluL3RlbXBsYXRlcy8xL2VkaXQiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0MToiaHR0cHM6Ly9iaXptYXJrLmlkL2FkbWluL3RlbXBsYXRlcy8xL2VkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763030308
3yAzvuPRf0cjb6cwypdhtHiXh9cSJcXXEPASCvQj	\N	103.156.118.4	Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWG9WRWpjNjZmOElGSjNQVllsWkl1TUdBdjhFRkczb1VkamZTdW1pZSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763032519
eQWR6vBdnF59jQIDzAXnhcVZyoRBJRfHABOConF5	\N	114.10.25.62	WhatsApp/2.23.20.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFdXVkNQYnNBVk5HUk81dUJPRk5HNU9UWTZmU2JHV2hud0tWblk0QSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763023234
9tOsUPtFOIQR4vtXGsjztxRUiFVTnumgk2fnqdXt	\N	114.10.64.188	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaXJURmxHYXNNbUwwUHplNklRcTJhZmVGNEFVZnZuSHh6akxjVGw0VyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763024743
lViXfPaSvvRuZyM48bgYOWzHkElq1bgmhqNlqTrL	\N	114.10.112.51	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNEdENWFJTFRqRlR1b3AwZENuak1iUHlGQjkxbDBtdnl1ZE5CS3BIdCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763027449
YWao8nCxfq94Sw2cErjP3P69zsnvOvkCVfy14Nnu	\N	159.89.124.69	Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaFhRZThlbFVtNVAzSHJYbFk2ZWxNc3JuYWIyMENhZHhSUHpNVTAwTyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763028988
wM1IH6lbgDKCbVbUQMr3yVboycxVXSZan6zj8FFj	\N	114.4.82.186	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoicGhqRFpJTzlaZkpQenE5S1JQUVpLYUxSb3FuMDc4N2xUUmpsM1JkdCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763030345
haZwMQB26d9QfTT9kmlFTHJ3vPbXpTGLpqp4V3m1	\N	180.244.166.239	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoia3hwbDE5V2ZMeGdsRGVhR2RObDV3QlR4WkI3MTNVVDNweHZCdHlwcyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763033073
vqfojtSE3EqkDrHShLz0QOik0znkAKPJBLUZ2t5T	\N	93.158.91.10	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoicDRCSjFVTFNmZUF0NEFIeUYyYnlVU2tNa0JaNEl6MUxGUFdnWnRRbyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763025042
RDEPiusiCEGbJTcBRrQyYkR6FE69qHAEE0DpipAd	\N	103.172.71.229	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidDQ2SmdEM01GOUJROXl2MkV4dE03R0NKdUp4cmlSVUxkdTFiTkdBQiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763023404
xvGwSJWv4kY8edvADlOdjXm6N9u8fQ0ShTowr7eO	\N	43.157.142.101	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSTl1QjBYNmpzOXkxUEZNQnBqZnZZSUk4UkFrNHNVQkhBQmJqb0dJUiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763027522
JGhjhhHDEXmU7dFkwpOst7B5hLZwPDI1qjQWWQI0	\N	136.107.66.62	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4240.193 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOEZrVFBkQmllWXVBeThYb2ppSVZ5VEQ5STJtbklKZk81aWJhdms3eiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763029144
SCMejpFrPe3uhqbMeRLenLrKk6gZ13TO72gvaJ37	\N	31.97.222.208	curl/8.14.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoiYURxaUh6UWc1Vm1rUWdBbk5ib0ZaTmFNZ09WQ2JzN2FvRmpCcWxQbSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxODoiaHR0cHM6Ly9iaXptYXJrLmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763031208
Avdd23chwQev0o77pwBeEKLlNlDDHG8e5Xm5S2Wk	\N	180.244.166.239	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ2hsSHQ2TXVuUFRnTlZ5Q2hiSDltSWlMTlpidnVQc2VqaGRXUkFWWiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763033643
yr82qerFuVzbjSgdDzwaTQnhkYR9D6E0JeUWecxA	\N	134.195.158.242	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoibmRoeERFUlY1cTNNbzZ4Z2NNcG9MY0FQdjBIOW1abWVQV3luZndpYSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwczovLzMxLjk3LjIyMi4yMDgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763023415
er3FaOnv9fhVe2I2Mk96FtWPcNBj3F2yu7rvMW0S	\N	114.8.206.33	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVHppRElCTk1mYndDQnZQbFZqWm1GR2hEMDdSMlpweEVJYzFhem1SbyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763025305
Ak8zmgVxezpb2gbl0k2kI0nuFQzb3AoPQv19STOg	\N	124.156.157.91	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS2kxMHRTNjZKSzcyS3ZPaDlQMUZkTzZSMlBlNXFnMUZMcnF1VzB0byI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763027649
YJFwW4FBbe4QNdGuqBVynQ0P3YDppUpp2RjhPcdg	\N	202.65.239.176	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoid2J0RTdOQXRTUjY2YThMMVRBbURFcjZhNzVySThsS1QyVkxGSlhHUCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763029235
Xlcc6gcOCOzcvMLeXA2l0sDDh4HxvYzQTPe7X9WJ	\N	31.97.222.208	curl/8.14.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoiTGhaU1R0Z041SW9FdFZwTXNyUjNWSlVDMzlhcnM4dGRBVU0xbkdPYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxODoiaHR0cHM6Ly9iaXptYXJrLmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763031261
UcjTA1WbPW0vFKHOUDiHEmZcqBOysP1GH01iHv3g	\N	14.235.226.121	Mozilla/5.0 (compatible; ZenixOpsBot/2.1; +https://zenixops.com)	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFRHRXFYM3FlbDB3Rnlqakg1ck8wUWRWVnRuYzVCN2NNUGtiQklmTCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763033809
8lP8OI1eBEGbWJLiPKh4TNzENavMTDSQzNto3fKt	\N	114.8.203.246	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/28.0 Chrome/130.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoibVRBRUt2NW1jV21FMzRsd1JzckIxck05TDNhUG9hWVdxMlZ2ZnlmbyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763025939
mOOEGZO8iWqmJJlhPSTkVYkhxsr4LPAJE6pd4J5u	\N	114.79.7.154	Mozilla/5.0 (iPhone; CPU iPhone OS 16_1_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.1 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoibzh5TE9RUjR0MFNoZ21pbTVrdloydVIxazAzWjluRGplYzFscDhxOCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763027761
L68liyXaT3RD4E3asFFmYgAal7OgFgsHK1nyT2Pu	\N	111.95.89.73	Mozilla/5.0 (iPhone; CPU iPhone OS 17_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNXlxMlRZaU9FR2F3a01nbzVQS0YyYjVha0M1TjVJTXIyazFVREVmVyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763029489
S57ZoA4BKoyqJOTpRilEruOEn3rvnNaGVizWjJCx	\N	103.133.27.71	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoicjJJWjdDZXRZWTd5RUtvOVpNZ1p5Qzk1VDFYZlo3MnE3SzRqMFo5NyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMyOiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvMS9hcHBseSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1763023527
lgq6e01lK6QKSTWTCme42w4m3wt8MTwbm34GAW4J	\N	125.160.230.76	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS1psWFgwWW03TVB1amV0QVU0Q0FWZks3V2NhS1dNWUQwckRJWmIzVCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763031609
ZDa5j2u3M9Fi5sCYwTHRDKTQn4YO1iSllVcYyu7O	\N	139.195.56.87	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRk5UVzVhbkR6OTI3UTNjNnB0U0VJamhLc01ncUREblJid3NsdlFwWCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763033906
APIclEWWL6I9mjJxe1Iy2QgC12LQ9n1Y2hLGsW0s	\N	114.8.209.20	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidUZNR0MzclhtT0Mxcm1hY2VzZmo3cUtyVGNUNDJTVnFoZElZVlNJMiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763023497
8Sr11geLSpFPZ61HQFDu3aQyAgT4PWyKE84yRUOc	\N	114.4.79.171	Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.7390.122 Safari/537.36 HIBrowser/v2.25.4.3	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUzN5Wkw2bXdGdWRZS1FHZURiTllIUDVrNGx1REkzVU9WOEZVTGJuQSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763026417
OTk5Uvm7flj2FEaTw71p1EtMFrFMAyBLr8LbhhpG	\N	31.97.222.208	curl/8.14.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoicE5oZ0VxSmpjVzV0cHkwMllDdEVIenZDQ2FMUDVmRTBuWDRtckhPVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxODoiaHR0cHM6Ly9iaXptYXJrLmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763029929
4BJxKtwQnNNkfuvifPaIe9UB4jRWCTuIJ0OflgSV	\N	94.247.172.129	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2)	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN29nNWtYRzBZdDRSeEU3ZmFEVGVkbld2blo3U2NCWTRZd2gwV0pZcSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIyOiJodHRwczovL3d3dy5iaXptYXJrLmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763031943
0BCDWJI9eQdxr3fQ4feUWQGhoKWHav6lzrCyI4AT	1	203.83.40.53	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRUtZV1dUc1pxeTZLaExOUUxVZVhrT1YxclFURUZmZ1FCdW9DSW9PeiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwczovL2Jpem1hcmsuaWQvYWRtaW4vZW1haWwvc2V0dGluZ3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc2MzAxNzY2MTt9fQ==	1763033588
H9zjajpWNLbIkWNrARD1h1FOq9hnIEtEf87l5zPd	\N	217.217.250.10	Go-http-client/2.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZzZ0NGk5UzlhOHhjV1ZrQ1g0czVkTVFhUkw3dExsYlVYbGs0NmhJZCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763030055
7XpRfEqGfQFWSrtLqVDSFi0IqVSm6tC6kg7iFmKt	\N	114.8.207.222	Mozilla/5.0 (iPhone; CPU iPhone OS 18_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.3 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU1l2VXliOE1jQjVuUUtzWmFaWkJtZkp3T2FrSERDRzlyWk1jNFVVQyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763034031
xK7Sx6ntl05RLNcxZLzB9tPVeNeZgMYYuBAmVNwp	\N	114.4.212.199	Mozilla/5.0 (Linux; U; Android 14; in-id; RMX3630 Build/UKQ1.230924.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.72 Mobile Safari/537.36 HeyTapBrowser/45.13.3.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMjJ2dGdXcEVoN3ZVVWJ0THozMkkwMGlVdjdSbnRHa0dTeHoxNkJnQSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMyOiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvMS9hcHBseSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1763026714
SKuaZQzRBlW2OCTGRksuXFjdcW455kT9N4Vaxqw4	\N	110.138.95.36	Mozilla/5.0 (iPhone; CPU iPhone OS 16_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoicnJ3TkJoVGZrRHQ0bWtkMldwSTdDamxMYXBvZGlWQ1Q5TWZaUGlrOCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763023571
wFtD08uhwathk24QojSAfICXKFmkcRofJjmQStKH	\N	110.138.89.116	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ1NPZWNQR1d6OVBET0Y5UTQ1aWxybEJtSEYzejRvSktOT05PRnpLUyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763023649
ik7iWw1FaAXTDG0SEaQSmRmR2lqKACCPpHkf1hcO	\N	34.244.74.33	Mozilla/5.0 (compatible; NetcraftSurveyAgent/1.0; +info@netcraft.com)	YTo0OntzOjY6Il90b2tlbiI7czo0MDoicDRtN25qbWx5UWxuTUpLWEFiM2oycmx6QlZQc3IwZFp1VzVMVUxFdCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763032046
EHHNs3mp5LUYXioneX2O3ptAhvpaxES5wuyzsmo7	\N	43.155.26.193	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoibkMwVllOdlRBUDJmc3lyb3JJNEgyS0hwcVg1T25ZcURJZlVyNUVjQSI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763034092
1FsXkJjcPJMCq4ycSFN9lZZEfkJQXDeKvq4jk02q	\N	182.0.209.80	WhatsApp/2.23.20.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoieFZXRTB6UUZlcll5NE5MRjU2QlNVaEhXRjVSMkFtT2w5ZGhEZ0dvTiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763022897
KiquSDAsy0jYY98hPb9MrbJ6ZJTWEo1iNEdSbOjr	\N	114.8.207.101	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidFF0WVNtbTh3TW53YUtkUllDSHFiakZZbG9oTXk2Z245eWUyZVdkZyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763026930
JEGmaNPpulMNsUuRBJ6R3Tk2bqH7KrujyQQd9Dtt	\N	195.178.110.201	Python/3.10 aiohttp/3.13.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiejE3QmNGQXJMck5sTFJCMmNnZkNndXozeFQ0ZG9wZThlVkl1Vnh3VyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763032241
hdcy3o6BbKUBaJNhrnv1YwpOqfXqrJgriYWrfdrS	\N	114.4.83.24	Mozilla/5.0 (iPhone; CPU iPhone OS 18_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/139.0.7258.76 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoieFZISGNpbndQSjcwcEVvbWkyeDh6QU9QbHFuTnBUckI4dFpGdEVqZyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763034309
kqWczzlzPxzJQAkk6uvheplTHE2jo7FgUbYB2H2W	\N	114.8.199.153	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYnFNaVh6VWtuNDJlYkZsdlFNbEc5cEtkcm5IY3lTNlRWSkFwakNibCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763027077
29nBnKhfqxo4GnSnp0njs9AcTBsZYxfIvK6zBeui	\N	114.10.25.62	Mozilla/5.0 (Linux; Android 13; Redmi 12 Build/TP1A.220624.014) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.118 Mobile Safari/537.36 XiaoMi/MiuiBrowser/14.45.0-gn	YTo0OntzOjY6Il90b2tlbiI7czo0MDoieEFvSlpFYldQOXNrT2ZuTlIzZTNnOU9Jd056OXZ1Y0NCSkpPRTNLWiI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMyOiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvMS9hcHBseSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1763023003
h8I8HLXHQVx9AekWgYFQuLGh9WmikHDCsJj76coX	\N	202.180.16.75	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaWg2TDVVdjllQjJoMDlxS0htWGtGRDlaa3VOY1VMWmlCMjBWMTNiNyI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763024736
1M0bsD3x7nFuXYuFW8Z28F0ezYA8TAKcC9ugBVJO	\N	109.105.209.30	Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.117 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXRCSnNvZ09oQTAxZm1heVV4UTlDWlNLQ3ZrYVRISnQ0NjlJODh6aCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjE4OiJodHRwczovL2Jpem1hcmsuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1763032250
TCNwJU0mDqrMyIsNzVOGZRVzLWhu8XkQZuaEcOnW	\N	180.244.162.167	Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVU5JYWtJOE9yYVNqZ2dpZElLa0dpVUJPY1VoMEI1b0hjQ2pMQUxvViI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvZHJhZnRlci1kb2t1bWVuLWxpbmdrdW5nYW4tdGVrbmlzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1763027222
QJdAXDEQJRDDCYmIPalcj3xY87OVAoFsPRfoRErT	\N	103.133.27.71	Mozilla/5.0 (iPhone; CPU iPhone OS 17_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWG5xeWF2REg5elJleG04UTg0MmhPUnhTemJ2WWVDemVDaDZpRTNoWCI7czo2OiJsb2NhbGUiO3M6MjoiaWQiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMyOiJodHRwczovL2Jpem1hcmsuaWQva2FyaXIvMS9hcHBseSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1763023209
\.


--
-- Data for Name: system_settings; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.system_settings (id, company_name, company_email, company_phone, company_website, company_address, maintenance_mode, email_notifications, created_at, updated_at) FROM stdin;
1	Bizmark.ID	support@bizmark.id	0812-1234-5678	https://bizmark.id	Jl. Contoh No. 123, Jakarta	f	t	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: tasks; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.tasks (id, project_id, title, description, sop_notes, assigned_user_id, due_date, started_at, completed_at, status, priority, institution_id, depends_on_task_id, completion_notes, estimated_hours, actual_hours, sort_order, created_at, updated_at, project_permit_id) FROM stdin;
1	1	Persiapan Dokumen Persyaratan	Mengumpulkan dan mempersiapkan semua dokumen persyaratan untuk pengajuan IMB	1. Surat permohonan\n2. KTP pemohon\n3. Surat kepemilikan tanah\n4. Gambar arsitektur\n5. Perhitungan struktur	2	2025-11-10	\N	\N	todo	high	1	\N	\N	16	\N	1	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
2	1	Konsultasi dengan Arsitek	Melakukan konsultasi teknis dengan arsitek untuk finalisasi desain	\N	4	2025-11-08	2025-11-01 12:33:14	\N	in_progress	normal	\N	\N	\N	8	\N	2	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
3	2	Analisis Dampak Lingkungan	Melakukan analisis dampak lingkungan untuk proses UKL-UPL	1. Survey lokasi\n2. Analisis dampak air\n3. Analisis dampak udara\n4. Rencana pengelolaan\n5. Rencana pemantauan	2	2025-11-17	\N	\N	todo	urgent	1	\N	\N	40	\N	1	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
4	2	Pengajuan ke DLHK	Pengajuan dokumen UKL-UPL ke Dinas Lingkungan Hidup dan Kehutanan	\N	5	2025-11-24	\N	\N	blocked	high	5	\N	\N	4	\N	2	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
5	3	Studi Lalu Lintas	Melakukan studi analisis dampak lalu lintas untuk pembangunan mall	1. Survey volume lalu lintas\n2. Analisis kapasitas jalan\n3. Prediksi dampak\n4. Rencana mitigasi\n5. Rekomendasi	2	2025-11-13	2025-10-31 12:33:14	\N	in_progress	high	\N	\N	\N	32	12	1	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
6	3	Koordinasi dengan Dishub	Koordinasi dan konsultasi dengan Dinas Perhubungan terkait analisis lalu lintas	\N	1	2025-11-18	\N	\N	todo	normal	1	\N	\N	6	\N	2	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
7	1	Pengajuan ke DPMPTSP	Pengajuan berkas IMB ke Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu	\N	4	2025-11-01	\N	\N	todo	urgent	6	\N	\N	3	\N	3	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
8	2	Finalisasi Dokumen UKL-UPL	Finalisasi dan review dokumen UKL-UPL sebelum pengajuan	\N	2	2025-11-06	2025-10-29 12:33:14	2025-11-02 12:33:14	done	normal	\N	\N	Dokumen telah selesai direview dan siap untuk pengajuan. Semua persyaratan teknis telah dipenuhi.	6	5	3	2025-11-03 12:33:14	2025-11-03 12:33:14	\N
\.


--
-- Data for Name: tax_rates; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.tax_rates (id, name, rate, description, is_default, is_active, sort_order, created_at, updated_at) FROM stdin;
1	Tanpa Pajak	0.00	Digunakan untuk transaksi tanpa pajak.	f	t	0	2025-11-03 12:33:13	2025-11-03 12:33:13
2	PPN 11%	11.00	Tarif PPN nasional 11%.	t	t	10	2025-11-03 12:33:13	2025-11-03 12:33:13
3	PPH 23 2%	2.00	Potongan PPH 23 sebesar 2%.	f	t	20	2025-11-03 12:33:13	2025-11-03 12:33:13
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: admin
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, full_name, "position", phone, is_active, last_login_at, notes, avatar, role_id) FROM stdin;
1	hadez	hadez@bizmark.id	2025-11-03 12:33:13	$2y$12$zkCefrOUYFMm/sF0yKVxMOS2IGRpf1bWmTjCQffqWIxWQ/nVQ5G3G	\N	2025-11-03 12:33:13	2025-11-03 12:33:13	Hadez Administrator	System Administrator	081234567890	t	\N	Administrator utama sistem	\N	1
2	manager	manager@bizmark.id	2025-11-03 12:33:13	$2y$12$X2hafzZQLffE3YMWPeVlxeLMrL4itgraLY4Xf3BANCvTW7fUu.9mq	\N	2025-11-03 12:33:13	2025-11-03 12:33:13	Budi Santoso	Project Manager	081234567891	t	\N	Manager proyek perizinan	\N	1
3	staff1	siti@bizmark.id	2025-11-03 12:33:13	$2y$12$CPc0FtyPHpSljNtrdeA9u.zAJLL2HLF8eom9LnwGqPlnQsz4amVqm	\N	2025-11-03 12:33:14	2025-11-03 12:33:14	Siti Aminah	Konsultan Senior	081234567892	t	\N	Konsultan perizinan lingkungan	\N	4
4	staff2	ahmad@bizmark.id	2025-11-03 12:33:13	$2y$12$9m4rEW9xLNIbs9O34sF9w.MRQ9dUNDg4Osv3icdid7Y2Oyy2p4/eK	\N	2025-11-03 12:33:14	2025-11-03 12:33:14	Ahmad Fadli	Konsultan Junior	081234567893	t	\N	Konsultan perizinan lalu lintas	\N	4
5	staff3	maya@bizmark.id	2025-11-03 12:33:13	$2y$12$sJenf2AIqNv3RfvpCL8MoOQMUDPGwpYeWhDTlg/QMy2p1Uo0PV0lu	\N	2025-11-03 12:33:14	2025-11-03 12:33:14	Maya Dewi	Document Controller	081234567894	t	\N	Pengendali dokumen dan administrasi	\N	4
\.


--
-- Name: ai_processing_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.ai_processing_logs_id_seq', 43, true);


--
-- Name: articles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.articles_id_seq', 1, false);


--
-- Name: bank_reconciliations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.bank_reconciliations_id_seq', 1, false);


--
-- Name: bank_statement_entries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.bank_statement_entries_id_seq', 1, false);


--
-- Name: cash_accounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.cash_accounts_id_seq', 1, false);


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.clients_id_seq', 1, false);


--
-- Name: compliance_checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.compliance_checks_id_seq', 1, true);


--
-- Name: document_drafts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.document_drafts_id_seq', 14, true);


--
-- Name: document_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.document_templates_id_seq', 3, true);


--
-- Name: documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.documents_id_seq', 7, true);


--
-- Name: email_campaigns_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.email_campaigns_id_seq', 1, false);


--
-- Name: email_inbox_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.email_inbox_id_seq', 1, false);


--
-- Name: email_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.email_logs_id_seq', 1, false);


--
-- Name: email_subscribers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.email_subscribers_id_seq', 3, true);


--
-- Name: email_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.email_templates_id_seq', 3, true);


--
-- Name: expense_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.expense_categories_id_seq', 29, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 15, true);


--
-- Name: institutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.institutions_id_seq', 6, true);


--
-- Name: invoice_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.invoice_items_id_seq', 1, false);


--
-- Name: invoices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.invoices_id_seq', 1, false);


--
-- Name: job_applications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.job_applications_id_seq', 3, true);


--
-- Name: job_vacancies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.job_vacancies_id_seq', 1, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.jobs_id_seq', 62, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.migrations_id_seq', 62, true);


--
-- Name: milestones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.milestones_id_seq', 1, false);


--
-- Name: payment_methods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.payment_methods_id_seq', 5, true);


--
-- Name: payment_schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.payment_schedules_id_seq', 1, false);


--
-- Name: permission_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permission_role_id_seq', 79, true);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permissions_id_seq', 32, true);


--
-- Name: permit_documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permit_documents_id_seq', 1, false);


--
-- Name: permit_template_dependencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permit_template_dependencies_id_seq', 1, false);


--
-- Name: permit_template_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permit_template_items_id_seq', 1, false);


--
-- Name: permit_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permit_templates_id_seq', 1, false);


--
-- Name: permit_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.permit_types_id_seq', 1, false);


--
-- Name: project_expenses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.project_expenses_id_seq', 1, false);


--
-- Name: project_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.project_logs_id_seq', 6, true);


--
-- Name: project_payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.project_payments_id_seq', 1, false);


--
-- Name: project_permit_dependencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.project_permit_dependencies_id_seq', 1, false);


--
-- Name: project_permits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.project_permits_id_seq', 1, false);


--
-- Name: project_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.project_statuses_id_seq', 11, true);


--
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.projects_id_seq', 6, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.roles_id_seq', 5, true);


--
-- Name: security_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.security_settings_id_seq', 1, true);


--
-- Name: system_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.system_settings_id_seq', 1, true);


--
-- Name: tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.tasks_id_seq', 8, true);


--
-- Name: tax_rates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.tax_rates_id_seq', 3, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: admin
--

SELECT pg_catalog.setval('public.users_id_seq', 5, true);


--
-- Name: ai_processing_logs ai_processing_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ai_processing_logs
    ADD CONSTRAINT ai_processing_logs_pkey PRIMARY KEY (id);


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: articles articles_slug_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT articles_slug_unique UNIQUE (slug);


--
-- Name: bank_reconciliations bank_reconciliations_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_reconciliations
    ADD CONSTRAINT bank_reconciliations_pkey PRIMARY KEY (id);


--
-- Name: bank_statement_entries bank_statement_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_statement_entries
    ADD CONSTRAINT bank_statement_entries_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cash_accounts cash_accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.cash_accounts
    ADD CONSTRAINT cash_accounts_pkey PRIMARY KEY (id);


--
-- Name: clients clients_email_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_email_unique UNIQUE (email);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: compliance_checks compliance_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.compliance_checks
    ADD CONSTRAINT compliance_checks_pkey PRIMARY KEY (id);


--
-- Name: document_drafts document_drafts_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts
    ADD CONSTRAINT document_drafts_pkey PRIMARY KEY (id);


--
-- Name: document_templates document_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_templates
    ADD CONSTRAINT document_templates_pkey PRIMARY KEY (id);


--
-- Name: documents documents_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_pkey PRIMARY KEY (id);


--
-- Name: email_campaigns email_campaigns_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_campaigns
    ADD CONSTRAINT email_campaigns_pkey PRIMARY KEY (id);


--
-- Name: email_inbox email_inbox_message_id_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_inbox
    ADD CONSTRAINT email_inbox_message_id_unique UNIQUE (message_id);


--
-- Name: email_inbox email_inbox_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_inbox
    ADD CONSTRAINT email_inbox_pkey PRIMARY KEY (id);


--
-- Name: email_logs email_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_logs
    ADD CONSTRAINT email_logs_pkey PRIMARY KEY (id);


--
-- Name: email_logs email_logs_tracking_id_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_logs
    ADD CONSTRAINT email_logs_tracking_id_unique UNIQUE (tracking_id);


--
-- Name: email_subscribers email_subscribers_email_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_subscribers
    ADD CONSTRAINT email_subscribers_email_unique UNIQUE (email);


--
-- Name: email_subscribers email_subscribers_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_subscribers
    ADD CONSTRAINT email_subscribers_pkey PRIMARY KEY (id);


--
-- Name: email_templates email_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_templates
    ADD CONSTRAINT email_templates_pkey PRIMARY KEY (id);


--
-- Name: expense_categories expense_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.expense_categories
    ADD CONSTRAINT expense_categories_pkey PRIMARY KEY (id);


--
-- Name: expense_categories expense_categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.expense_categories
    ADD CONSTRAINT expense_categories_slug_unique UNIQUE (slug);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: institutions institutions_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.institutions
    ADD CONSTRAINT institutions_pkey PRIMARY KEY (id);


--
-- Name: invoice_items invoice_items_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoice_items
    ADD CONSTRAINT invoice_items_pkey PRIMARY KEY (id);


--
-- Name: invoices invoices_invoice_number_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_invoice_number_unique UNIQUE (invoice_number);


--
-- Name: invoices invoices_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_pkey PRIMARY KEY (id);


--
-- Name: job_applications job_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: job_vacancies job_vacancies_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_vacancies
    ADD CONSTRAINT job_vacancies_pkey PRIMARY KEY (id);


--
-- Name: job_vacancies job_vacancies_slug_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_vacancies
    ADD CONSTRAINT job_vacancies_slug_unique UNIQUE (slug);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: milestones milestones_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.milestones
    ADD CONSTRAINT milestones_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: payment_methods payment_methods_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_code_unique UNIQUE (code);


--
-- Name: payment_methods payment_methods_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_methods
    ADD CONSTRAINT payment_methods_pkey PRIMARY KEY (id);


--
-- Name: payment_schedules payment_schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_schedules
    ADD CONSTRAINT payment_schedules_pkey PRIMARY KEY (id);


--
-- Name: permission_role permission_role_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_pkey PRIMARY KEY (id);


--
-- Name: permission_role permission_role_role_id_permission_id_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_role_id_permission_id_unique UNIQUE (role_id, permission_id);


--
-- Name: permissions permissions_name_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_unique UNIQUE (name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: permit_documents permit_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_documents
    ADD CONSTRAINT permit_documents_pkey PRIMARY KEY (id);


--
-- Name: permit_template_dependencies permit_template_dependencies_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_dependencies
    ADD CONSTRAINT permit_template_dependencies_pkey PRIMARY KEY (id);


--
-- Name: permit_template_items permit_template_items_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_items
    ADD CONSTRAINT permit_template_items_pkey PRIMARY KEY (id);


--
-- Name: permit_templates permit_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_templates
    ADD CONSTRAINT permit_templates_pkey PRIMARY KEY (id);


--
-- Name: permit_types permit_types_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_types
    ADD CONSTRAINT permit_types_code_unique UNIQUE (code);


--
-- Name: permit_types permit_types_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_types
    ADD CONSTRAINT permit_types_pkey PRIMARY KEY (id);


--
-- Name: project_expenses project_expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_expenses
    ADD CONSTRAINT project_expenses_pkey PRIMARY KEY (id);


--
-- Name: project_logs project_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_logs
    ADD CONSTRAINT project_logs_pkey PRIMARY KEY (id);


--
-- Name: project_payments project_payments_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments
    ADD CONSTRAINT project_payments_pkey PRIMARY KEY (id);


--
-- Name: project_permit_dependencies project_permit_dep_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies
    ADD CONSTRAINT project_permit_dep_unique UNIQUE (project_permit_id, depends_on_permit_id);


--
-- Name: project_permit_dependencies project_permit_dependencies_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies
    ADD CONSTRAINT project_permit_dependencies_pkey PRIMARY KEY (id);


--
-- Name: project_permits project_permits_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits
    ADD CONSTRAINT project_permits_pkey PRIMARY KEY (id);


--
-- Name: project_statuses project_statuses_code_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_statuses
    ADD CONSTRAINT project_statuses_code_unique UNIQUE (code);


--
-- Name: project_statuses project_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_statuses
    ADD CONSTRAINT project_statuses_pkey PRIMARY KEY (id);


--
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: roles roles_name_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_unique UNIQUE (name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: security_settings security_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.security_settings
    ADD CONSTRAINT security_settings_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: system_settings system_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.system_settings
    ADD CONSTRAINT system_settings_pkey PRIMARY KEY (id);


--
-- Name: tasks tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_pkey PRIMARY KEY (id);


--
-- Name: tax_rates tax_rates_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tax_rates
    ADD CONSTRAINT tax_rates_pkey PRIMARY KEY (id);


--
-- Name: job_applications unique_email_per_job; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT unique_email_per_job UNIQUE (job_vacancy_id, email);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: ai_processing_logs_created_at_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX ai_processing_logs_created_at_index ON public.ai_processing_logs USING btree (created_at);


--
-- Name: ai_processing_logs_project_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX ai_processing_logs_project_id_index ON public.ai_processing_logs USING btree (project_id);


--
-- Name: ai_processing_logs_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX ai_processing_logs_status_index ON public.ai_processing_logs USING btree (status);


--
-- Name: articles_category_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX articles_category_index ON public.articles USING btree (category);


--
-- Name: articles_is_featured_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX articles_is_featured_index ON public.articles USING btree (is_featured);


--
-- Name: articles_status_published_at_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX articles_status_published_at_index ON public.articles USING btree (status, published_at);


--
-- Name: articles_title_content_fulltext; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX articles_title_content_fulltext ON public.articles USING gin (((to_tsvector('english'::regconfig, (title)::text) || to_tsvector('english'::regconfig, content))));


--
-- Name: bank_reconciliations_cash_account_id_reconciliation_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX bank_reconciliations_cash_account_id_reconciliation_date_index ON public.bank_reconciliations USING btree (cash_account_id, reconciliation_date);


--
-- Name: bank_reconciliations_start_date_end_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX bank_reconciliations_start_date_end_date_index ON public.bank_reconciliations USING btree (start_date, end_date);


--
-- Name: bank_reconciliations_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX bank_reconciliations_status_index ON public.bank_reconciliations USING btree (status);


--
-- Name: bank_statement_entries_is_matched_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX bank_statement_entries_is_matched_index ON public.bank_statement_entries USING btree (is_matched);


--
-- Name: bank_statement_entries_reconciliation_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX bank_statement_entries_reconciliation_id_index ON public.bank_statement_entries USING btree (reconciliation_id);


--
-- Name: bank_statement_entries_transaction_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX bank_statement_entries_transaction_date_index ON public.bank_statement_entries USING btree (transaction_date);


--
-- Name: cash_accounts_account_type_is_active_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX cash_accounts_account_type_is_active_index ON public.cash_accounts USING btree (account_type, is_active);


--
-- Name: clients_email_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX clients_email_index ON public.clients USING btree (email);


--
-- Name: clients_name_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX clients_name_index ON public.clients USING btree (name);


--
-- Name: clients_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX clients_status_index ON public.clients USING btree (status);


--
-- Name: compliance_checks_draft_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX compliance_checks_draft_id_index ON public.compliance_checks USING btree (draft_id);


--
-- Name: compliance_checks_overall_score_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX compliance_checks_overall_score_index ON public.compliance_checks USING btree (overall_score);


--
-- Name: compliance_checks_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX compliance_checks_status_index ON public.compliance_checks USING btree (status);


--
-- Name: document_drafts_project_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX document_drafts_project_id_index ON public.document_drafts USING btree (project_id);


--
-- Name: document_drafts_project_id_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX document_drafts_project_id_status_index ON public.document_drafts USING btree (project_id, status);


--
-- Name: document_drafts_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX document_drafts_status_index ON public.document_drafts USING btree (status);


--
-- Name: document_templates_is_active_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX document_templates_is_active_index ON public.document_templates USING btree (is_active);


--
-- Name: document_templates_permit_type_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX document_templates_permit_type_index ON public.document_templates USING btree (permit_type);


--
-- Name: documents_project_id_category_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX documents_project_id_category_index ON public.documents USING btree (project_id, category);


--
-- Name: documents_status_is_latest_version_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX documents_status_is_latest_version_index ON public.documents USING btree (status, is_latest_version);


--
-- Name: documents_uploaded_by_created_at_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX documents_uploaded_by_created_at_index ON public.documents USING btree (uploaded_by, created_at);


--
-- Name: email_campaigns_scheduled_at_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_campaigns_scheduled_at_index ON public.email_campaigns USING btree (scheduled_at);


--
-- Name: email_campaigns_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_campaigns_status_index ON public.email_campaigns USING btree (status);


--
-- Name: email_inbox_category_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_inbox_category_index ON public.email_inbox USING btree (category);


--
-- Name: email_inbox_from_email_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_inbox_from_email_index ON public.email_inbox USING btree (from_email);


--
-- Name: email_inbox_is_read_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_inbox_is_read_index ON public.email_inbox USING btree (is_read);


--
-- Name: email_inbox_received_at_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_inbox_received_at_index ON public.email_inbox USING btree (received_at);


--
-- Name: email_inbox_to_email_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_inbox_to_email_index ON public.email_inbox USING btree (to_email);


--
-- Name: email_logs_campaign_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_logs_campaign_id_index ON public.email_logs USING btree (campaign_id);


--
-- Name: email_logs_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_logs_status_index ON public.email_logs USING btree (status);


--
-- Name: email_logs_subscriber_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_logs_subscriber_id_index ON public.email_logs USING btree (subscriber_id);


--
-- Name: email_logs_tracking_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_logs_tracking_id_index ON public.email_logs USING btree (tracking_id);


--
-- Name: email_subscribers_email_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_subscribers_email_index ON public.email_subscribers USING btree (email);


--
-- Name: email_subscribers_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_subscribers_status_index ON public.email_subscribers USING btree (status);


--
-- Name: email_templates_category_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_templates_category_index ON public.email_templates USING btree (category);


--
-- Name: email_templates_is_active_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX email_templates_is_active_index ON public.email_templates USING btree (is_active);


--
-- Name: invoice_items_invoice_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX invoice_items_invoice_id_index ON public.invoice_items USING btree (invoice_id);


--
-- Name: invoices_invoice_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX invoices_invoice_date_index ON public.invoices USING btree (invoice_date);


--
-- Name: invoices_invoice_number_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX invoices_invoice_number_index ON public.invoices USING btree (invoice_number);


--
-- Name: invoices_project_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX invoices_project_id_index ON public.invoices USING btree (project_id);


--
-- Name: invoices_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX invoices_status_index ON public.invoices USING btree (status);


--
-- Name: job_applications_email_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX job_applications_email_index ON public.job_applications USING btree (email);


--
-- Name: job_applications_job_vacancy_id_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX job_applications_job_vacancy_id_status_index ON public.job_applications USING btree (job_vacancy_id, status);


--
-- Name: job_applications_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX job_applications_status_index ON public.job_applications USING btree (status);


--
-- Name: job_vacancies_deadline_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX job_vacancies_deadline_index ON public.job_vacancies USING btree (deadline);


--
-- Name: job_vacancies_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX job_vacancies_status_index ON public.job_vacancies USING btree (status);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: payment_schedules_due_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX payment_schedules_due_date_index ON public.payment_schedules USING btree (due_date);


--
-- Name: payment_schedules_invoice_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX payment_schedules_invoice_id_index ON public.payment_schedules USING btree (invoice_id);


--
-- Name: payment_schedules_project_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX payment_schedules_project_id_index ON public.payment_schedules USING btree (project_id);


--
-- Name: payment_schedules_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX payment_schedules_status_index ON public.payment_schedules USING btree (status);


--
-- Name: permit_documents_project_permit_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_documents_project_permit_id_index ON public.permit_documents USING btree (project_permit_id);


--
-- Name: permit_template_dependencies_template_id_permit_item_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_template_dependencies_template_id_permit_item_id_index ON public.permit_template_dependencies USING btree (template_id, permit_item_id);


--
-- Name: permit_template_items_template_id_sequence_order_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_template_items_template_id_sequence_order_index ON public.permit_template_items USING btree (template_id, sequence_order);


--
-- Name: permit_templates_category_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_templates_category_index ON public.permit_templates USING btree (category);


--
-- Name: permit_templates_is_public_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_templates_is_public_index ON public.permit_templates USING btree (is_public);


--
-- Name: permit_types_category_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_types_category_index ON public.permit_types USING btree (category);


--
-- Name: permit_types_is_active_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX permit_types_is_active_index ON public.permit_types USING btree (is_active);


--
-- Name: project_expenses_expense_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_expenses_expense_date_index ON public.project_expenses USING btree (expense_date);


--
-- Name: project_expenses_project_id_expense_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_expenses_project_id_expense_date_index ON public.project_expenses USING btree (project_id, expense_date);


--
-- Name: project_logs_entity_type_entity_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_logs_entity_type_entity_id_index ON public.project_logs USING btree (entity_type, entity_id);


--
-- Name: project_logs_project_id_created_at_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_logs_project_id_created_at_index ON public.project_logs USING btree (project_id, created_at);


--
-- Name: project_logs_user_id_action_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_logs_user_id_action_index ON public.project_logs USING btree (user_id, action);


--
-- Name: project_payments_payment_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_payments_payment_date_index ON public.project_payments USING btree (payment_date);


--
-- Name: project_payments_project_id_payment_date_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_payments_project_id_payment_date_index ON public.project_payments USING btree (project_id, payment_date);


--
-- Name: project_permit_dependencies_project_permit_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_permit_dependencies_project_permit_id_index ON public.project_permit_dependencies USING btree (project_permit_id);


--
-- Name: project_permits_project_id_sequence_order_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_permits_project_id_sequence_order_index ON public.project_permits USING btree (project_id, sequence_order);


--
-- Name: project_permits_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX project_permits_status_index ON public.project_permits USING btree (status);


--
-- Name: projects_client_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX projects_client_id_index ON public.projects USING btree (client_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: tasks_assigned_user_id_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX tasks_assigned_user_id_status_index ON public.tasks USING btree (assigned_user_id, status);


--
-- Name: tasks_due_date_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX tasks_due_date_status_index ON public.tasks USING btree (due_date, status);


--
-- Name: tasks_project_id_status_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX tasks_project_id_status_index ON public.tasks USING btree (project_id, status);


--
-- Name: tasks_project_permit_id_index; Type: INDEX; Schema: public; Owner: admin
--

CREATE INDEX tasks_project_permit_id_index ON public.tasks USING btree (project_permit_id);


--
-- Name: ai_processing_logs ai_processing_logs_document_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ai_processing_logs
    ADD CONSTRAINT ai_processing_logs_document_id_foreign FOREIGN KEY (document_id) REFERENCES public.documents(id) ON DELETE SET NULL;


--
-- Name: ai_processing_logs ai_processing_logs_initiated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ai_processing_logs
    ADD CONSTRAINT ai_processing_logs_initiated_by_foreign FOREIGN KEY (initiated_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: ai_processing_logs ai_processing_logs_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ai_processing_logs
    ADD CONSTRAINT ai_processing_logs_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: ai_processing_logs ai_processing_logs_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.ai_processing_logs
    ADD CONSTRAINT ai_processing_logs_template_id_foreign FOREIGN KEY (template_id) REFERENCES public.document_templates(id) ON DELETE CASCADE;


--
-- Name: articles articles_author_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT articles_author_id_foreign FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: bank_reconciliations bank_reconciliations_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_reconciliations
    ADD CONSTRAINT bank_reconciliations_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bank_reconciliations bank_reconciliations_cash_account_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_reconciliations
    ADD CONSTRAINT bank_reconciliations_cash_account_id_foreign FOREIGN KEY (cash_account_id) REFERENCES public.cash_accounts(id) ON DELETE CASCADE;


--
-- Name: bank_reconciliations bank_reconciliations_reconciled_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_reconciliations
    ADD CONSTRAINT bank_reconciliations_reconciled_by_foreign FOREIGN KEY (reconciled_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bank_reconciliations bank_reconciliations_reviewed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_reconciliations
    ADD CONSTRAINT bank_reconciliations_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: bank_statement_entries bank_statement_entries_reconciliation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.bank_statement_entries
    ADD CONSTRAINT bank_statement_entries_reconciliation_id_foreign FOREIGN KEY (reconciliation_id) REFERENCES public.bank_reconciliations(id) ON DELETE CASCADE;


--
-- Name: compliance_checks compliance_checks_draft_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.compliance_checks
    ADD CONSTRAINT compliance_checks_draft_id_foreign FOREIGN KEY (draft_id) REFERENCES public.document_drafts(id) ON DELETE CASCADE;


--
-- Name: document_drafts document_drafts_ai_log_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts
    ADD CONSTRAINT document_drafts_ai_log_id_foreign FOREIGN KEY (ai_log_id) REFERENCES public.ai_processing_logs(id) ON DELETE CASCADE;


--
-- Name: document_drafts document_drafts_approved_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts
    ADD CONSTRAINT document_drafts_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: document_drafts document_drafts_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts
    ADD CONSTRAINT document_drafts_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: document_drafts document_drafts_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts
    ADD CONSTRAINT document_drafts_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: document_drafts document_drafts_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_drafts
    ADD CONSTRAINT document_drafts_template_id_foreign FOREIGN KEY (template_id) REFERENCES public.document_templates(id) ON DELETE CASCADE;


--
-- Name: document_templates document_templates_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.document_templates
    ADD CONSTRAINT document_templates_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: documents documents_parent_document_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_parent_document_id_foreign FOREIGN KEY (parent_document_id) REFERENCES public.documents(id);


--
-- Name: documents documents_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: documents documents_task_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_task_id_foreign FOREIGN KEY (task_id) REFERENCES public.tasks(id) ON DELETE SET NULL;


--
-- Name: documents documents_uploaded_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.documents
    ADD CONSTRAINT documents_uploaded_by_foreign FOREIGN KEY (uploaded_by) REFERENCES public.users(id);


--
-- Name: email_campaigns email_campaigns_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_campaigns
    ADD CONSTRAINT email_campaigns_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: email_campaigns email_campaigns_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_campaigns
    ADD CONSTRAINT email_campaigns_template_id_foreign FOREIGN KEY (template_id) REFERENCES public.email_templates(id) ON DELETE SET NULL;


--
-- Name: email_inbox email_inbox_assigned_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_inbox
    ADD CONSTRAINT email_inbox_assigned_to_foreign FOREIGN KEY (assigned_to) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: email_inbox email_inbox_replied_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_inbox
    ADD CONSTRAINT email_inbox_replied_to_foreign FOREIGN KEY (replied_to) REFERENCES public.email_inbox(id) ON DELETE SET NULL;


--
-- Name: email_logs email_logs_campaign_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_logs
    ADD CONSTRAINT email_logs_campaign_id_foreign FOREIGN KEY (campaign_id) REFERENCES public.email_campaigns(id) ON DELETE CASCADE;


--
-- Name: email_logs email_logs_subscriber_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.email_logs
    ADD CONSTRAINT email_logs_subscriber_id_foreign FOREIGN KEY (subscriber_id) REFERENCES public.email_subscribers(id) ON DELETE CASCADE;


--
-- Name: invoice_items invoice_items_invoice_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoice_items
    ADD CONSTRAINT invoice_items_invoice_id_foreign FOREIGN KEY (invoice_id) REFERENCES public.invoices(id) ON DELETE CASCADE;


--
-- Name: invoices invoices_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: job_applications job_applications_job_vacancy_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_job_vacancy_id_foreign FOREIGN KEY (job_vacancy_id) REFERENCES public.job_vacancies(id) ON DELETE CASCADE;


--
-- Name: job_applications job_applications_reviewed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.job_applications
    ADD CONSTRAINT job_applications_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES public.users(id);


--
-- Name: payment_schedules payment_schedules_invoice_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_schedules
    ADD CONSTRAINT payment_schedules_invoice_id_foreign FOREIGN KEY (invoice_id) REFERENCES public.invoices(id) ON DELETE SET NULL;


--
-- Name: payment_schedules payment_schedules_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_schedules
    ADD CONSTRAINT payment_schedules_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: payment_schedules payment_schedules_reconciliation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.payment_schedules
    ADD CONSTRAINT payment_schedules_reconciliation_id_foreign FOREIGN KEY (reconciliation_id) REFERENCES public.bank_reconciliations(id) ON DELETE SET NULL;


--
-- Name: permission_role permission_role_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: permission_role permission_role_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: permit_documents permit_documents_project_permit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_documents
    ADD CONSTRAINT permit_documents_project_permit_id_foreign FOREIGN KEY (project_permit_id) REFERENCES public.project_permits(id) ON DELETE CASCADE;


--
-- Name: permit_documents permit_documents_uploaded_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_documents
    ADD CONSTRAINT permit_documents_uploaded_by_foreign FOREIGN KEY (uploaded_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: permit_template_dependencies permit_template_dependencies_depends_on_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_dependencies
    ADD CONSTRAINT permit_template_dependencies_depends_on_item_id_foreign FOREIGN KEY (depends_on_item_id) REFERENCES public.permit_template_items(id) ON DELETE CASCADE;


--
-- Name: permit_template_dependencies permit_template_dependencies_permit_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_dependencies
    ADD CONSTRAINT permit_template_dependencies_permit_item_id_foreign FOREIGN KEY (permit_item_id) REFERENCES public.permit_template_items(id) ON DELETE CASCADE;


--
-- Name: permit_template_dependencies permit_template_dependencies_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_dependencies
    ADD CONSTRAINT permit_template_dependencies_template_id_foreign FOREIGN KEY (template_id) REFERENCES public.permit_templates(id) ON DELETE CASCADE;


--
-- Name: permit_template_items permit_template_items_permit_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_items
    ADD CONSTRAINT permit_template_items_permit_type_id_foreign FOREIGN KEY (permit_type_id) REFERENCES public.permit_types(id) ON DELETE SET NULL;


--
-- Name: permit_template_items permit_template_items_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_template_items
    ADD CONSTRAINT permit_template_items_template_id_foreign FOREIGN KEY (template_id) REFERENCES public.permit_templates(id) ON DELETE CASCADE;


--
-- Name: permit_templates permit_templates_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_templates
    ADD CONSTRAINT permit_templates_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: permit_types permit_types_institution_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.permit_types
    ADD CONSTRAINT permit_types_institution_id_foreign FOREIGN KEY (institution_id) REFERENCES public.institutions(id) ON DELETE SET NULL;


--
-- Name: project_expenses project_expenses_bank_account_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_expenses
    ADD CONSTRAINT project_expenses_bank_account_id_foreign FOREIGN KEY (bank_account_id) REFERENCES public.cash_accounts(id) ON DELETE SET NULL;


--
-- Name: project_expenses project_expenses_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_expenses
    ADD CONSTRAINT project_expenses_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: project_expenses project_expenses_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_expenses
    ADD CONSTRAINT project_expenses_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: project_expenses project_expenses_reconciliation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_expenses
    ADD CONSTRAINT project_expenses_reconciliation_id_foreign FOREIGN KEY (reconciliation_id) REFERENCES public.bank_reconciliations(id) ON DELETE SET NULL;


--
-- Name: project_logs project_logs_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_logs
    ADD CONSTRAINT project_logs_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: project_logs project_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_logs
    ADD CONSTRAINT project_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: project_payments project_payments_bank_account_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments
    ADD CONSTRAINT project_payments_bank_account_id_foreign FOREIGN KEY (bank_account_id) REFERENCES public.cash_accounts(id) ON DELETE SET NULL;


--
-- Name: project_payments project_payments_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments
    ADD CONSTRAINT project_payments_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: project_payments project_payments_invoice_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments
    ADD CONSTRAINT project_payments_invoice_id_foreign FOREIGN KEY (invoice_id) REFERENCES public.invoices(id) ON DELETE SET NULL;


--
-- Name: project_payments project_payments_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments
    ADD CONSTRAINT project_payments_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: project_payments project_payments_reconciliation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_payments
    ADD CONSTRAINT project_payments_reconciliation_id_foreign FOREIGN KEY (reconciliation_id) REFERENCES public.bank_reconciliations(id) ON DELETE SET NULL;


--
-- Name: project_permit_dependencies project_permit_dependencies_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies
    ADD CONSTRAINT project_permit_dependencies_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: project_permit_dependencies project_permit_dependencies_depends_on_permit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies
    ADD CONSTRAINT project_permit_dependencies_depends_on_permit_id_foreign FOREIGN KEY (depends_on_permit_id) REFERENCES public.project_permits(id) ON DELETE CASCADE;


--
-- Name: project_permit_dependencies project_permit_dependencies_overridden_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies
    ADD CONSTRAINT project_permit_dependencies_overridden_by_user_id_foreign FOREIGN KEY (overridden_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: project_permit_dependencies project_permit_dependencies_project_permit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permit_dependencies
    ADD CONSTRAINT project_permit_dependencies_project_permit_id_foreign FOREIGN KEY (project_permit_id) REFERENCES public.project_permits(id) ON DELETE CASCADE;


--
-- Name: project_permits project_permits_assigned_to_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits
    ADD CONSTRAINT project_permits_assigned_to_user_id_foreign FOREIGN KEY (assigned_to_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: project_permits project_permits_existing_document_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits
    ADD CONSTRAINT project_permits_existing_document_id_foreign FOREIGN KEY (existing_document_id) REFERENCES public.documents(id) ON DELETE SET NULL;


--
-- Name: project_permits project_permits_override_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits
    ADD CONSTRAINT project_permits_override_by_user_id_foreign FOREIGN KEY (override_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: project_permits project_permits_permit_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits
    ADD CONSTRAINT project_permits_permit_type_id_foreign FOREIGN KEY (permit_type_id) REFERENCES public.permit_types(id) ON DELETE SET NULL;


--
-- Name: project_permits project_permits_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.project_permits
    ADD CONSTRAINT project_permits_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: projects projects_client_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_client_id_foreign FOREIGN KEY (client_id) REFERENCES public.clients(id) ON DELETE SET NULL;


--
-- Name: projects projects_institution_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_institution_id_foreign FOREIGN KEY (institution_id) REFERENCES public.institutions(id);


--
-- Name: projects projects_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.project_statuses(id);


--
-- Name: tasks tasks_assigned_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_assigned_user_id_foreign FOREIGN KEY (assigned_user_id) REFERENCES public.users(id);


--
-- Name: tasks tasks_depends_on_task_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_depends_on_task_id_foreign FOREIGN KEY (depends_on_task_id) REFERENCES public.tasks(id);


--
-- Name: tasks tasks_institution_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_institution_id_foreign FOREIGN KEY (institution_id) REFERENCES public.institutions(id);


--
-- Name: tasks tasks_project_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_project_id_foreign FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- Name: tasks tasks_project_permit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_project_permit_id_foreign FOREIGN KEY (project_permit_id) REFERENCES public.project_permits(id) ON DELETE SET NULL;


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: admin
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict oaRbZkpuTGbvBhldZkfgCxNEWd2zAIeVpLroj7FRPPnc7aomrFiiMi76VyN1zdU

