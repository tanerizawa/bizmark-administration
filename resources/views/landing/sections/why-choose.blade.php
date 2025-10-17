<!-- Why Choose Us Section -->
<section id="about" class="section bg-white md:bg-[#F7F8FC]">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">
                {{ app()->getLocale() == 'id' ? 'Mengapa Kami' : 'Why Us' }}
            </span>
            <h2 class="section-title mb-6">
                {{ app()->getLocale() == 'id' ? 'Mengapa Memilih Bizmark.ID?' : 'Why Choose Bizmark.ID?' }}
            </h2>
            <p class="section-description max-w-3xl mx-auto">
                {{ app()->getLocale() == 'id' ? 'Keunggulan yang membuat kami menjadi pilihan terbaik untuk perizinan industri Anda' : 'Advantages that make us the best choice for your industrial permits' }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
            <div class="card text-center group" data-aos="zoom-in" data-aos-delay="0">
                <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-bolt text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Proses Cepat' : 'Fast Process' }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Dengan pengalaman 10+ tahun dan jaringan luas, kami memastikan proses perizinan Anda selesai tepat waktu' : 'With 10+ years experience and wide network, we ensure your permit process completes on time' }}
                </p>
            </div>
            
            <div class="card text-center group" data-aos="zoom-in" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-secondary to-secondary-dark rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-eye text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Transparan' : 'Transparent' }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Update progress berkala memungkinkan Anda memantau setiap tahap proses tanpa biaya tersembunyi' : 'Regular progress updates allow you to monitor every stage without hidden costs' }}
                </p>
            </div>
            
            <div class="card text-center group" data-aos="zoom-in" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-certificate text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Terpercaya' : 'Trusted' }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Dipercaya oleh berbagai perusahaan untuk menangani perizinan industri dengan profesional dan akurat' : 'Trusted by various companies to handle industrial permits professionally and accurately' }}
                </p>
            </div>
            
            <div class="card text-center group" data-aos="zoom-in" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-accent to-accent-dark rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-users text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Tim Berpengalaman' : 'Experienced Team' }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Konsultan bersertifikat dengan pengalaman menangani berbagai jenis industri di seluruh Indonesia' : 'Certified consultants experienced in handling various industries across Indonesia' }}
                </p>
            </div>
            
            <div class="card text-center group" data-aos="zoom-in" data-aos-delay="400">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-headset text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Support Responsif' : 'Responsive Support' }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Tim customer support kami siap membantu Anda melalui WhatsApp, telepon, atau email dengan respon cepat' : 'Our customer support team ready to help via WhatsApp, phone, or email with fast response' }}
                </p>
            </div>
            
            <div class="card text-center group" data-aos="zoom-in" data-aos-delay="500">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-handshake text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Jaminan Kepuasan' : 'Satisfaction Guarantee' }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Kami berkomitmen memberikan layanan terbaik dengan jaminan kepuasan untuk setiap klien kami' : 'We are committed to providing the best service with satisfaction guarantee for every client' }}
                </p>
            </div>
        </div>
    </div>
</section>
