pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Project Information') {
            steps {
                sh '''
                    echo "Current Directory:"
                    pwd

                    echo ""
                    echo "Project Files:"
                    ls -la
                '''
            }
        }

        stage('PHP Version') {
            steps {
                sh 'php -v'
            }
        }

    }

    post {
        always {
            echo 'Pipeline Finished.'
        }

        success {
            echo 'Pipeline Successful.'
        }

        failure {
            echo 'Pipeline Failed.'
        }
    }
}
